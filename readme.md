# Scm
Home page display the event list of the `current year` ordered by categories.  
Display each category to see its historic.

At least, create a division and and season to be able to create a new event

## Administration
- Choose to hide left and/or right column on the website
- Set users authorizations  
    - read | write | moderate | Manage clubs | Manage divisions | Manage seasons | Manage event setup  
    - can be refined for each category

## Categories
The player categories (U11, U13, U15, ...) or National, International , Friendly, ...

## Club
Define Clubs that will becomes teams in events (Clubs could be countries)

## Division
- The name of the event (Champions League, World Cup, ...)
- Event type
    - Championship = all teams vs all teams, day by day
    - Cup = knockout games
    - Tournament = group stage then knockout games
- Games type
    - single games
    - return games

## Season
the year of the event
- Select the first year
- Checkbox to define the year of the event  
    if it's
    - checked => calendar year (eg: 2023)
    - unchecked => two years (eg: 2023-2024)

## Event

### Creation
- Select a category
- Select a division
- Select a season
- Define if it's display or not (+ delayed display)
    useful to prepare it before displaying it

#### Event setup
Depends on event type  
On event start only the Teams setup is available.
Valid one setup releave the next one:  
    Teams 
    > Parameter 
        > Groups (Tournament) OR Days (Championship + cup) 
            > Day games (championship + cup) OR Group games tournament) + bracket games (Cup + Tournament)

#### Teams Setup
Select clubs as teams participating to the event

#### Parameters Setup
- Tournament (Tournament only)
    - Teams per round (number)
    - Hat-ranking type
    - Fill the games (checkbox)  
        if checked, all games will be filled with teams when groups are validated else, if unchecked, each games could be filled manualy
    - looser bracket  
        if checked, a looser bracket will be added to the finals stage and all places will be assigned (from first of winner bracket to last of looser bracket)
    - display playgrounds
- Bracket stage (Cup & Tournament)
    - Rounds number (eg: if 4 => round of 16, round of 8, Semin-finals, Final)
    - Games with overtime  
        if checked
        - overtime duration
    <!-- - Gold goal -->
    <!-- - Silver goal -->
    - Game for third place  
        (in tournament, available only if looser bracket is unchecked)
- Rankings (Championship & Tournament)
    - Points for a victory
    - Points for a draw
    - Points for a loss
    - Number of promoted teams + define background color
    - Number of playoff teams + define background color
    - Number of relegated teams + define background color
    <!-- - Select type of ranking -->
- Miscelaneous (All event type)
    - Game duration
    - Select a favourite team (from only those of the event)

#### Groups Setup (tournament)
The number of groups is calculated by divided the number of teams selected in `Teams Setup`, by the number of teams per group defined in the `Parameters Setup`
    - Select all teams for each group  
On validation, if `Fill the games` is checked in params, teams of each group are assigned automatically for each game of the group 

#### Group Games Setup (tournament)
Fill the date | playground | team 1 | score 1 | score 2 | team 2 | for each game of each group

#### Bracket Games Setup (cup tournament)
Fill the date | playground | team 1 | score 1 | score 2 | team 2 | for each game of each round

### Display the event

#### Calendar

##### Cup & Tournament
Displays all games (with results) ordered by date and stage

<!-- ##### Championship
Displays all games ordered by day or schedule -->

#### Rankings
##### Tournament
Displays results and rankings ordered by groups or `hat-ranking`

##### Cup & Tournament
Displays results ordered in a tree view

<!-- ##### Championship
Displays rankings in a standings table -->



