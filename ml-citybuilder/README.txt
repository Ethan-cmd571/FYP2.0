ML CityBuilder — Full Working Build

Contents
- index.php
- api.php
- app.js
- style.css
- config.php
- ml_citybuilder.sql

Setup
1. Put this folder into your XAMPP htdocs directory.
2. Start Apache and MySQL.
3. Create a database named ml_citybuilder.
4. Import ml_citybuilder.sql into that database.
5. Open http://localhost/<folder-name>/

Notes
- This build removes manual tile-map placement from the UI.
- Buildings are still stored in city_tiles behind the scenes so the original database schema works.
- Correct ML answers unlock and auto-place buildings into districts.
- The skyline grows automatically as progress is made.


Upgrade patch notes
- Houses are now unlocked from the start so the first district feels active immediately.
- Automatic learning analytics are tracked per user: challenges started, attempts, correct answers, accuracy, hints used, lesson acknowledgements, boosters used, and average response time.
- Topic mastery is stored in user_topic_progress.
- The API auto-creates the analytics tables on first run, so no manual SQL migration is required.


Hotfix v3
- Removed forced localStorage session restore, so the app no longer auto-logs into a previous/random user.
- Added an automatic starter house in Central District whenever a user has no placed buildings.
- Early auto-placement now prioritizes houses until the first district visibly grows.


Hotfix v4
- District progress bars now use both learning progression and actual visible buildings.
- As districts gain placed buildings, their progress bars rise more directly so the UI better matches what players can see.
