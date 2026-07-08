<?php
/**
 * @copyright   &copy; 2005-2026 PHPBoost
 * @license     https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL-3.0
 * @author      Regis VIARRE <crowkait@phpboost.com>
 * @version     PHPBoost 6.1 - last update: 2026 05 19
 * @since       PHPBoost 1.6 - 2007 08 23
 * @author      Arnaud GENET <elenwii@phpboost.com>
 * @author      Sebastien LARTIGUE <babsolune@phpboost.com>
*/

define('PATH_TO_ROOT', '..');

require_once PATH_TO_ROOT . '/kernel/init.php';
require_once PATH_TO_ROOT . '/kernel/header.php';

$previewRows = [];
$headers = [];
$message = '';
$process = 'error';

if (isset($_POST['preview'])) {
    if (!empty($_FILES['clubs_file']['tmp_name'])) {
        $csvFile = $_FILES['clubs_file']['tmp_name'];

        if (($handle = fopen($csvFile, 'r')) !== false) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, '|', '"', '\\')) !== false) {
                if ($row === 0) {
                    $headers = $data;
                } else {
                    $previewRows[] = $data;
                }
                $row++;
            }
            fclose($handle);
        } else {
            $message = "Impossible d'ouvrir le fichier CSV.";
        }
    } else {
        $message = "Aucun fichier CSV envoyé.";
    }
}

if (isset($_POST['import_selected'])) {
    if (!empty($_FILES['clubs_file']['tmp_name']) && !empty($_POST['selected_rows'])) {
        $csvFile = $_FILES['clubs_file']['tmp_name'];
        $selectedRows = array_map('intval', $_POST['selected_rows']);
        $selectedRows = array_flip($selectedRows);

        if (($handle = fopen($csvFile, 'r')) !== false) {
            $row = 0;
            while (($data = fgetcsv($handle, 1000, '|', '"', '\\')) !== false) {
                if ($row === 0) {
                    $row++;
                    continue;
                }

                if (isset($selectedRows[$row])) {
                    PersistenceContext::get_querier()->insert(ScmSetup::$scm_club_table, [
                        'id_club'        => null,
                        'club_name'      => $data[0] ?? null,
                        'club_slug'      => $data[0] ? Url::encode_rewrite($data[0]) : null,
                        'club_number'    => $data[2] ?? null,
                        'club_full_name' => $data[1] ?? null,
                        'club_logo'      => $data[3] ?? 'modules/scm/templates/images/badges/club.webp',
                        'club_flag'      => $data[4] ?? null,
                        'club_website'   => $data[5] ?? null,
                        'club_email'     => $data[6] ?? null,
                        'club_phone'     => $data[7] ?? null,
                    ]);
                }

                $row++;
            }
            fclose($handle);
            $message = "Import terminé.";
            $process = 'success';
        } else {
            $message = "Impossible d'ouvrir le fichier CSV.";
        }
    } else {
        $message = "Aucune ligne sélectionnée ou aucun fichier CSV.";
        $process = 'warning';
    }
}
?>

<?php if ($message): ?>
    <div class="message-helper bgc <?= $process ?>"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<form action="index.php?token=<?= AppContext::get_session()->get_token() ?>" method="post" enctype="multipart/form-data">
    <label for="clubs_file">Choisir un fichier CSV :</label>
    <input type="file" name="clubs_file" id="clubs_file" accept=".csv" required>

    <button class="button submit" type="submit" name="preview">Prévisualiser</button>

    <?php if (!empty($previewRows)): ?>
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
                    <th>N° FFF</th>
                    <th>Logo</th>
                    <th>Drapeau</th>
                    <th>Site</th>
                    <th>Email</th>
                    <th>Tel</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($previewRows as $index => $row): ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="selected_rows[]" value="<?= $index + 1 ?>" class="row-checkbox">
                        </td>
                        <td><?= htmlspecialchars($row[0] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row[1] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row[2] ?? '-') ?></td>
                        <td><?php if (htmlspecialchars($row[3])) : ?> <img src="<?= PATH_TO_ROOT . '/' . htmlspecialchars($row[3]) ?>" width="64" height="64" alt="logo du club <?= htmlspecialchars($row[1] ?? '') ?>"><?php else : ?>-<?php endif; ?></td>
                        <td><?php if (htmlspecialchars($row[4])) : ?> <img src="<?=  PATH_TO_ROOT ?>/images/stats/countries/<?= htmlspecialchars($row[4]) ?>.png" alt="Drapeau du club <?= htmlspecialchars($row[1]) ?>"><?php else : ?>-<?php endif; ?></td>
                        <td><?= htmlspecialchars($row[5] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row[6] ?? '-') ?></td>
                        <td><?= htmlspecialchars($row[7] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <br>
        <button class="button submit" type="submit" name="import_selected">Importer la sélection</button>
    <?php endif; ?>
</form>

<script>
function selectAllRows(state) {
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = state);
}
</script>
<?php

require_once PATH_TO_ROOT . '/kernel/footer.php';