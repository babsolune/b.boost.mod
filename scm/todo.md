# TODO scm
[ ] non fait  
[X] fait  
[-] en cours (fait partiellement)  
## Evo
- [X] Rename module from `Competition` to `Scm` (Sport Competition Manager)
- [X] Check if a team is not twice or more in a group
- [X] Check if all teams have the same number of games
- [X] Check if all teams have the same number of games at home and away
### Tournament
- [ ] get the group stage qualified team list on bracket form page
- [ ] limit the team list in select to the group stage qualified team list on first round bracket form page
- [ ] limit the team list in select to previous round qualified team list on other rounds bracket form page
- [X] modify the looser-bracket to set several brackets instead of only one (bracket 1, bracket 2, bracket 3)
- [-] add rounds for games (1st matchday, 2nd matchday, ...)
    - [ ] hat ranking
### Games form
- [X] rebuild css to fit mobile, columns and set/bonus options
## Ranking
- [-] set filters for draw ranks (actually write hard in code) (see draw-rules.md)
- [X] reset caches on details validation
### Clubs
- [ ] self division
- [X] Country flag
### Params
- [X] If it's Championship, add a section for team penalties
- [X] provide half-time in match duration

## Database
- [ ] Rename columns to make them shorter

## bugs
- [X] For 2 legs games, group stage, if not all games results are filled, a new line is added in ranking (for the team 0, I guess)
- [X] Date for bracket on game creation must be start_date
- [X] Match for third place
- [X] save goal list on details
## Event home
- [X] bg color for ongoing games
- [X] Ongoing games list
- [X] Next games list
