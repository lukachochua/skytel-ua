# SkyTel USER AREA

## Description
This project implements a user authentication system with various features, including password reset functionality, social media authentication, and user profile management. It is built with Laravel and utilizes Bootstrap for styling.

## Table of Contents
- [Installation](#installation)
- [Usage](#usage)
- [Features](#features)
- [Contributing](#contributing)
- [License](#license)

## Installation
To set up the project locally, follow these steps:

1. Clone the repository:
   ```bash
   git clone https://github.com/your-username/your-repo.git
   cd your-repo
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Set up the environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate the application key:
   ```bash
   php artisan key:generate
   ```

5. Run migrations:
   ```bash
   php artisan migrate
   ```

6. Start the local development server:
   ```bash
   php artisan serve
   ```

## Usage
Open your browser and navigate to `http://localhost:8000`. You can register a new account or log in using the provided options. For password resets, follow the link provided on the login page.

## Features
### Recent Commits
- **feat: implement reset password functionality via smtp**  
  Implemented SMTP email functionality to allow users to reset their passwords.

- **style: several changes to layout and login blade files**  
  Improved layout consistency across login-related pages.

- **feat: implement standard user registration logic, without using Google and/or Facebook auth service**  
  Established a standard user registration process that does not rely on external authentication services.

- **feat: create user profile page and add all relevant fields to the form**  
  Created a user profile page with fields for user information.

- **style: small changes to buttons**  
  Made minor adjustments to button styles for better appearance.

- **feat: implement facebook authentication, first steps**  
  Began the implementation of Facebook authentication.

- **feat: add bootstrap to the project, move all css to app.css dedicated file**  
  Integrated Bootstrap into the project and organized CSS files.

- **style: form styling**  
  Improved the styling of forms for a better user experience.

- **style: improve style of all existing pages - login, dashboard, form**  
  Enhanced the visual presentation of key pages.

- **chore: clean up routes**  
  Organized and cleaned up the routing structure.

- **feat: correctly handle redirects, if user_info is not provided**  
  Implemented proper redirect handling for users without information.

- **feat: create form to save user info, after they log in with google auth**  
  Added a form to collect user information after Google authentication.

## Contributing
Contributions are welcome! Please open an issue or submit a pull request for any suggestions or improvements.

## License
This project is licensed under the MIT License - see the LICENSE file for details.
