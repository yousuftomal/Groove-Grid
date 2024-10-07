# Groove Grid

**Groove Grid** is a music streaming platform with different user roles (Listener, Artist, Admin). Artists can upload songs, Admins can approve them, and Listeners can stream and download songs. Listeners can subscribe to various packages and keep track of liked songs on their profile.

## Features
- **User Roles**: Listener, Artist, Admin
  - **Artist**: Uploads songs for review.
  - **Admin**: Approves uploaded songs.
  - **Listener**: Subscribes to a package, listens to and downloads approved songs.
- **Subscription Plans**: Monthly, 6-month, or yearly subscriptions for Listeners.
- **Like Feature**: Listeners can like songs, and the liked songs are viewable in their profile.

## Installation Instructions
1. Install **XAMPP**.
2. Clone or download this repository and copy it into the `htdocs` folder inside XAMPP.
3. Open **phpMyAdmin** in your browser using `localhost/phpmyadmin`.
4. Create a new database named `music_streaming`.
5. Copy the provided SQL code from this repo into the SQL editor to set up the database.
6. Navigate to `localhost/your-directory-name/index.php` in your browser to start the app.

## Usage Instructions
1. Make sure **XAMPP** is running, specifically Apache and MySQL services.
2. Follow the installation steps to set up the database.
3. Visit the site using `localhost/directory-name/index.php` and start using the platform.

## Technologies Used
- **Frontend**: HTML, CSS
- **Backend**: PHP
- **Database**: MySQL
- **Development Environment**: XAMPP

## License
This project is licensed under the MIT License.

## Future Improvements / Roadmap
- Move the platform to a working live server.
- Integrate a storage server for storing songs.
- Enhance the music player with more features.

## Contributors
- **Yousuf Tomal** (GitHub: [yousuftomal](https://github.com/yousuftomal))
- **Afrida Afaf** (GitHub: [afridaafaf](https://github.com/afridaafaf))
