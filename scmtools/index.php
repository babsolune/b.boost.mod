<?php
/**
 * @copyright   © 2005-2026 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Regis VIARRE <crowkait@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2026 05 19
 * @since       PHPBoost 1.6 - 2007 08 23
 * @author      Arnaud GENET <elenwii@phpboost.com>
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
*/

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/init.php';
define('TITLE', 'ScmTools');
require_once PATH_TO_ROOT . '/kernel/header.php';
session_start();
$message = '';
$process = 'error';
$conflict_rows = [];
$countries_file = PATH_TO_ROOT . '/lang/english/countries.php';

function build_serialized_district(?string $csv_value): string
{
    if (empty($csv_value)) {
        return '';
    }

    $countries_file = PATH_TO_ROOT . '/lang/english/countries.php';
    $lang = [];

    if (file_exists($countries_file)) {
        require $countries_file;
    }

    $parts = explode('_', trim($csv_value));
    $code = $parts[0] ?? '';
    $lref = $parts[1] ?? '';
    $dref = $parts[2] ?? '';

    if (isset($lang[$code]) && $lang[$code] != 0)
    {
        $country_file_name = Url::encode_rewrite($lang[$code]);
        $filePath = '../../../../modules/scm/data/leagues/' . $country_file_name . '.json';

        $structure = [
            [
                'code' => $code,
                'lref' => $lref,
                'dref' => $dref,
                'file' => $filePath
            ]
        ];
    } else {
        $structure = [];
    }

    return serialize($structure);
}

function build_logo(?string $name, ?string $csv_value): string
{
    if (empty($name) || empty($csv_value)) {
        return '';
    }

    $countries_file = PATH_TO_ROOT . '/lang/english/countries.php';
    $lang = [];

    if (file_exists($countries_file)) {
        require $countries_file;
    }

    $parts = explode('_', trim($csv_value));
    $code = $parts[0] ?? '';
    $lref = $parts[1] ?? '';
    $dref = $parts[2] ?? '';

    $country = isset($lang[$code]) ? '/' . Url::encode_rewrite($lang[$code]) : '';
    $league = isset($lref) && $lref != '' ? '/' . $lref : '';
    $district = isset($dref) && $dref != '' ? '/' . $dref : '';
    $club = '/' . $name . '.webp';

    if ($code === '0')
        return '';
    return '/modules/scm/data/logos' . $country . $league . $district . $club;
}

function build_flag(?string $csv_value): string 
{
    if (empty($csv_value)) {
        return '';
    }

    $parts = explode('_', trim($csv_value));
    if ($parts[0] === '0')
        $code = $parts[1] ?? '';
    else
        $code = $parts[0] ?? '';

    return $code;
}

/** @var array $lang */
$countries_file = new File(PATH_TO_ROOT . '/lang/english/countries.php');
$lang_countries = [];

if ($countries_file->exists()) {
    require $countries_file->get_path();
    $lang_countries = $lang;
}

// ---------------------------------------------------------------------
// IMPORT PROCESSING LOGIC
// ---------------------------------------------------------------------
if (isset($_POST['import_selected'])) {
    $data_to_process = [];
    $selected_rows = array_map('intval', $_POST['selected_rows'] ?? []);
    $selected_rows_set = array_flip($selected_rows);
    $overwrite_actions = $_POST['overwrite_actions'] ?? [];

    // CASE 1: First submit with file upload
    if (!empty($_FILES['clubs_file']['tmp_name']))
    {
        $csvFile = $_FILES['clubs_file']['tmp_name'];

        if (($handle = fopen($csvFile, 'r')) !== false)
        {
            $row = 0;
            $cached_rows = [];

            while (($data = fgetcsv($handle, 1000, '|', '"', '\\')) !== false) {
                // Ignorer l'en-tête ou ligne vide
                if ($row === 0 || empty($data[0]) || trim($data[0]) === '') {
                    $row++;
                    continue;
                }

                // On vérifie si l'index courant de la ligne est sélectionné
                if (isset($selected_rows_set[$row])) {
                    $cached_rows[$row] = $data;
                }
                $row++;
            }
            fclose($handle);

            $_SESSION['scm_import_rows'] = $cached_rows;
        }
    }

    // Retrieve cached rows
    $cached_rows = $_SESSION['scm_import_rows'] ?? [];

    if (!empty($cached_rows) && !empty($selected_rows)) {
        $has_conflicts = false;

        foreach ($cached_rows as $row_idx => $data) {
            if (isset($selected_rows_set[$row_idx])) {
                $club_name = $data[0] ?? '';

                $exists = PersistenceContext::get_querier()->row_exists(
                    ScmSetup::$scm_club_table, 
                    'WHERE club_name = :name', 
                    ['name' => $club_name]
                );

                if ($exists && !isset($overwrite_actions[$row_idx])) {
                    $has_conflicts = true;
                    $conflict_rows[$row_idx] = $data;
                }

                $data_to_process[$row_idx] = [
                    'data' => $data,
                    'exists' => $exists
                ];
            }
        }

        if ($has_conflicts && !isset($_POST['confirm_conflicts'])) {
            $message = "Des clubs existent déjà en base de données. Choisissez l'action à effectuer pour chaque doublon.";
            $process = 'warning';
        } else {
            $inserted = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($data_to_process as $row_idx => $item) {
                $data = $item['data'];
                $exists = $item['exists'];
                $action = $overwrite_actions[$row_idx] ?? 'update';
                $logo_file = build_logo(Url::encode_rewrite($data[0] ?? ''), $data[2] ?? null);

                $payload = [
                    'club_name'      => $data[0] ?? null,
                    'club_slug'      => !empty($data[1]) ? Url::encode_rewrite($data[1]) : null,
                    'club_full_name' => $data[1] ?? null,
                    'club_district'  => isset($data[2]) ? build_serialized_district($data[2]) : null,
                    'club_flag'      => isset($data[2]) ? build_flag($data[2]) : null,
                    'club_number'    => $data[3] ?? null,
                    'club_logo'      => isset($data[2]) ? $logo_file : '/modules/scm/templates/images/badges/club.webp',
                    'club_website'   => $data[4] ?? null,
                    'club_email'     => $data[5] ?? null,
                    'club_phone'     => $data[6] ?? null,
                ];

                if (!$exists) {
                    $payload['id_club'] = null;
                    PersistenceContext::get_querier()->insert(ScmSetup::$scm_club_table, $payload);
                    $inserted++;
                } else {
                    if ($action === 'update') {
                        PersistenceContext::get_querier()->update(
                            ScmSetup::$scm_club_table,
                            $payload,
                            'WHERE club_name = :name',
                            ['name' => $data[0]]
                        );
                        $updated++;
                    } else {
                        $skipped++;
                    }
                }
            }

            // Clear session data
            unset($_SESSION['scm_import_rows']);

            $message = "Import terminé ($inserted ajout(s), $updated mise(s) à jour, $skipped ignoré(s)).";
            $process = 'success';
        }
    } else {
        $message = "Aucune ligne sélectionnée ou aucun fichier transmis.";
        $process = 'warning';
    }
}

if (AppContext::get_current_user()->check_level(User::ADMINISTRATOR_LEVEL)) {
?>

    <?php if ($message): ?>
        <div class="message-helper bgc <?= $process ?>"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <!-- Modal / Conflict Resolution -->
    <?php if (!empty($conflict_rows)): ?>
        <form action="index.php?token=<?= AppContext::get_session()->get_token() ?>" method="post">
            <input type="hidden" name="confirm_conflicts" value="1">
            <input type="hidden" name="import_selected" value="1">

            <h3>Gestion des doublons détectés</h3>
            <p>Les clubs suivants existent déjà en base de données. Que souhaitez-vous faire ?</p>

            <table>
                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Club</th>
                        <th>District</th>
                        <th>N° FFF</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($conflict_rows as $row_idx => $row_data): ?>
                        <tr>
                            <td>
                                <select name="overwrite_actions[<?= $row_idx ?>]">
                                    <option value="update">Mettre à jour le club existant</option>
                                    <option value="skip">Ignorer ce club</option>
                                </select>
                            </td>
                            <td><?= htmlspecialchars($row_data[0] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row_data[2] ?? '-') ?></td>
                            <td><?= htmlspecialchars($row_data[3] ?? '-') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <?php foreach ($_POST['selected_rows'] as $selected_row): ?>
                <input type="hidden" name="selected_rows[]" value="<?= htmlspecialchars($selected_row) ?>">
            <?php endforeach; ?>

            <br>
            <button class="button submit" type="submit">Valider et finaliser l'importation</button>
        </form>
    <?php else: ?>

        <!-- Main Form -->
        <form action="index.php?token=<?= AppContext::get_session()->get_token() ?>" method="post" enctype="multipart/form-data" id="import_form">
            <label for="clubs_file">Choisir un fichier CSV :</label>
            <input type="file" name="clubs_file" id="clubs_file" accept=".csv" required onchange="parseCSVPreview()">

            <div id="preview_container" style="display: none; margin-top: 15px;">
                <div class="actions">
                    <button class="button default" type="button" onclick="selectAllRows(true)">Tout sélectionner</button>
                    <button class="button default" type="button" onclick="selectAllRows(false)">Tout désélectionner</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Sélection</th>
                            <th>Nom d'affichage</th>
                            <th>Nom Complet</th>
                            <th>District</th>
                            <th>Logo</th>
                            <th>N° FFF</th>
                            <th>Drapeau</th>
                            <th>Site</th>
                            <th>Email</th>
                            <th>Tel</th>
                        </tr>
                    </thead>
                    <tbody id="preview_tbody">
                        <!-- JS populated -->
                    </tbody>
                </table>

                <br>
                <button class="button submit" type="submit" name="import_selected" value="1">Importer la sélection</button>
            </div>
        </form>

    <?php endif; ?>

    <script>
    const pathToRoot = '<?= PATH_TO_ROOT ?>';
    const countriesMap = <?= json_encode($lang_countries, JSON_UNESCAPED_UNICODE) ?>;

    function parseCSVPreview() {
        const fileInput = document.getElementById('clubs_file');
        const container = document.getElementById('preview_container');
        const tbody = document.getElementById('preview_tbody');

        if (!fileInput.files.length) {
            container.style.display = 'none';
            return;
        }

        const file = fileInput.files[0];
        const reader = new FileReader();

        reader.onload = function(e) {
            const text = e.target.result;
            const lines = text.split(/\r\n|\n/);
            tbody.innerHTML = '';

            for (let i = 0; i < lines.length; i++) {
                const line = lines[i].trim();
                if (!line) continue;

                const rowData = parseCSVLine(line, '|', '"');

                // On ignore l'en-tête (première ligne non vide ou i === 0)
                if (i === 0 || rowData[0] === '') {
                    continue;
                }

                const tr = document.createElement('tr');

                const displayName = escapeHtml(rowData[0] || '-');
                const fullName = escapeHtml(rowData[1] || '-');
                const districtRaw = escapeHtml(rowData[2] || '-');
                const nFFF = escapeHtml(rowData[3] || '-');
                const site = escapeHtml(rowData[4] || '-');
                const email = escapeHtml(rowData[5] || '-');
                const tel = escapeHtml(rowData[6] || '-');
                let code = '';
                let lref = '';
                let dref = '';
                let logoHtml = '';
                let flagHtml = '';
                let u_code = '';
                let u_lref = '';
                let u_dref = '';
                let u_logo = '';

                const parts = districtRaw ? districtRaw.split('_') : [];
                if (districtRaw)
                {
                    if (parts[0] == 0)
                    {
                        code = parts[1];
                        lref = '-';
                        dref = '-';
                        logoHtml = '-';
                        flagHtml = `<img src="${pathToRoot}/images/stats/countries/${code}.png" alt="drapeau">`;
                    }
                    else
                    {
                        u_code = parts[0] ? '/' + countriesMap[parts[0]] : '';
                        u_lref = parts[1] ? '/' + parts[1] : '';
                        u_dref = parts[2] ? '/' + parts[2] : '';
                        u_logo = '/' + encode_rewrite(displayName) + '.webp';
                        code = parts[0] || '';
                        lref = parts[1] || '';
                        dref = parts[2] || '';
                        logoHtml = code != '-' ? `<img src="${pathToRoot}/modules/scm/data/logos/${encode_rewrite(u_code)}${u_lref}${u_dref}${u_logo}" width="32" height="32" alt="logo du club ${displayName}">` : '-';
                        flagHtml = code != '-' ? `<img src="${pathToRoot}/images/stats/countries/${code}.png" alt="drapeau">` : '-';
                    }
                }
                else
                {
                    code = '-';
                    lref = '-';
                    dref = '-';
                    logoHtml = '-';
                    flagHtml = '-';
                }

                tr.innerHTML = `
                    <td>
                        <input type="checkbox" name="selected_rows[]" value="${i}" class="row-checkbox" checked>
                    </td>
                    <td>${displayName}</td>
                    <td>${fullName}</td>
                    <td>${code}|${lref}|${dref}</td>
                    <td>${logoHtml}</td>
                    <td>${nFFF}</td>
                    <td>${flagHtml}</td>
                    <td>${site}</td>
                    <td>${email}</td>
                    <td>${tel}</td>
                `;

                tbody.appendChild(tr);
            }

            container.style.display = 'block';
        };

        reader.readAsText(file);
    }

    function parseCSVLine(text, delimiter = '|', quoteChar = '"') {
        const result = [];
        let currentCell = '';
        let inQuotes = false;

        for (let i = 0; i < text.length; i++) {
            const char = text[i];

            if (char === quoteChar) {
                inQuotes = !inQuotes;
            } else if (char === delimiter && !inQuotes) {
                result.push(currentCell.trim());
                currentCell = '';
            } else {
                currentCell += char;
            }
        }
        result.push(currentCell.trim());
        return result;
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function selectAllRows(state) {
        document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = state);
    }
    </script>
<?php
} else {
?>
    <div class="message-helper bgc-full error">Cet outil est réservé aux administrateurs</div>
<?php
}
require_once PATH_TO_ROOT . '/kernel/footer.php';