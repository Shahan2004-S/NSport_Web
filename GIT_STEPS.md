# Git steps for every member (do these once, then repeat 3 to 6)

1. Clone:        git clone <repo-url>      (the project leader gives you the link)
                 cd <repo-folder>
2. Your name:    git config user.name "Your Full Name"
                 git config user.email "your-github-email@example.com"
3. Your branch:  git checkout -b member6-events-fixtures-calendar
4. Work ONLY on your own files (listed in README_MEMBER6.md). Test in XAMPP.
5. Save small:   git add <your files>
                 git commit -m "Short clear message, e.g. Add booking validation"
6. Upload:       git push -u origin member6-events-fixtures-calendar
7. Open a Pull Request on GitHub. The leader merges it.
Before you start each day:  git pull origin main

Make MANY small commits (one feature each). The marking checks that the commit history shows every member's work.
