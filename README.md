# Nättraby Vägmuseum Website

This project is a museum website built using PHP. It showcases the history of roads, railways, waterways, and winter roads in Nättraby, Sweden. The website is designed to be modular, responsive, and user-friendly.

## Features
- **Dynamic Content**: Content is fetched from a SQLite database to ensure easy updates and scalability.
- **Responsive Design**: The website is optimized for various devices using CSS media queries and flexible layouts.
- **Modular Codebase**: The project is organized into separate directories for configuration, styles, scripts, and views to ensure maintainability.
- **Interactive Pages**: Includes features like user authentication, search functionality, and dynamic road displays.

## Directory Structure
- **config/**: Contains configuration files like `config.php` for database connections and global settings.
- **css/**: Includes stylesheets for base styles, components, layout, and utilities.
- **db/**: Stores the SQLite database files containing the museum's content.
- **img/**: Contains images used throughout the website, organized by resolution and purpose.
- **logs/**: Stores log files for debugging and monitoring.
- **public/**: Contains PHP scripts for pages accessible to users, such as `home.php`, `about.php`, and `login.php`.
- **src/**: Includes helper functions and libraries for database operations, HTML rendering, and authentication.
- **view/**: Contains reusable views like headers, footers, and navigation menus.

## Live Version
You can access the live version of the website [here](https://www.student.bth.se/~nibo23/dbwebb-kurser/webtec/me/proj/public/home.php).
