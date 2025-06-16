# PCS Twenty Twenty-Five Child – AI Editing & Deployment Guide  
*A single, self-contained reference for both humans and AI agents.*

---

## 0 · Quick Synopsis  
You are editing a **child theme** of Twenty Twenty-Five in a Local WP install.  
All edits happen **only** in `wp-content/themes/pcs-twentytwentyfive-child`, follow WordPress Coding Standards, get committed to Git, and auto-deploy to Hostinger via its Git-pull feature. Use `theme.json` for visual/styling changes. Smoke-test each deploy with WP-CLI.

---

## 1 · Environment & Key Paths  

| Purpose | Path |
|---------|------|
| WordPress root (local) | `~/Local Sites/pcsnew/app/public` |
| Child theme (editable) | `wp-content/themes/pcs-twentytwentyfive-child` |
| Parent theme (read-only) | `wp-content/themes/twentytwentyfive` |

> **WP-CLI note:** run commands from the WP root or add  
> `--path="~/Local Sites/pcsnew/app/public"`.

---

## 2 · Hard Rules for the AI  

1. **Edit only the child-theme folder.**  
2. **Enqueue the parent stylesheet** in `functions.php`  

   ```php
   add_action( 'wp_enqueue_scripts', function () {
       wp_enqueue_style(
           'twentytwentyfive-style',
           get_template_directory_uri() . '/style.css'
       );
   } );

	3.	Use theme.json for colors, fonts, spacing, and block styles.
	4.	Lint every commit with PHPCS

phpcs --standard=WordPress .


	5.	Keep block markup valid: <!-- wp:* --> … <!-- /wp:* -->.
	6.	Version bump: update the Version: header in style.css on every release.

⸻

3 · Hands-On Example Instructions

3.1 Create the Child Theme

cd ~/Local\ Sites/pcsnew/app/public/wp-content/themes
mkdir pcs-twentytwentyfive-child && cd $_

# style.css
cat <<'CSS' > style.css
/*
Theme Name: PCS Twenty Twenty-Five Child
Template: twentytwentyfive
Version: 0.1.0
*/
CSS

# functions.php
cat <<'PHP' > functions.php
<?php
add_action( 'wp_enqueue_scripts', function () {
    wp_enqueue_style(
        'twentytwentyfive-style',
        get_template_directory_uri() . '/style.css'
    );
} );
PHP

# copy theme.json for customization
cp ../twentytwentyfive/theme.json .

3.2 Init Git & Push to GitHub

git init
git add .
git commit -m "feat: bootstrap PCS child theme"
git remote add origin <YOUR_GITHUB_REPO_URL>
git branch -M main
git push -u origin main

3.3 Hostinger Auto-Deploy
	1.	hPanel → Git → “Connect Repository” → paste the repo URL.
	2.	Target dir: public_html/wp-content/themes/pcs-twentytwentyfive-child.
	3.	Enable “Automatic deployment on push”.

⸻

4 · Workflow Diagram

graph TD
    A(Local edit) --> B(Validate PHPCS & wp theme validate)
    B -->|pass| C(git commit)
    C --> D(git push main)
    D --> E(Hostinger auto-pull)
    E --> F(Live site runs new theme)


⸻

5 · Exporting Site-Editor Changes
	1.	Install Create Block Theme locally.
	2.	In the Site Editor: ⚙️ → Save changes to theme (writes files).
	3.	Commit the new/updated files and push to GitHub.

⸻

6 · Smoke-Test Commands (after each deploy)

wp theme list --status=active          # child theme must be active
wp core verify-checksums               # confirms core files unchanged
wp eval 'echo "✅  Theme looks good";'  # simple sanity ping


⸻

7 · Prompt Template for AI

Role: WordPress theme engineer
Context:
• WP root = ~/Local Sites/pcsnew/app/public
• Editable path = wp-content/themes/pcs-twentytwentyfive-child
Constraints:
	1.	Edit only within the child-theme directory.
	2.	Must pass phpcs --standard=WordPress . and keep block HTML valid.
	3.	Use theme.json for any visual change (colors, typography, spacing).
	4.	Always enqueue parent CSS as shown above.
	5.	After edits: run wp theme validate, bump version, commit, push main.
Example task:
“Add brand color #2669e8 to theme.json palette and set Buttons block backgroundColor to that preset.”

⸻

8 · Key References
	•	WordPress Child-Theme Handbook
	•	Parent-style enqueue pattern
	•	theme.json reference & tutorials
	•	WordPress Coding Standards & PHPCS
	•	Block HTML grammar basics
	•	WP-CLI documentation
	•	Create Block Theme plugin docs
	•	Hostinger Git auto-deploy guide