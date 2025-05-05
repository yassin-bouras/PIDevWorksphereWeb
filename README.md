Worksphere
Worksphere is a comprehensive human resources management application developed using Symfony and MySQL, designed to help companies efficiently manage their HR-related administrative tasks. It provides a suite of features to streamline employee management, event organization, training sessions, project oversight, and more. This project was developed as part of the PIDEV 3A course at Esprit School of Engineering, focusing on advanced web development and AI integration.
Table of Contents

Features
Installation
Usage
Contributing
License
Contact
Technologies Used
Acknowledgements

Features

User Authentication and Management: Secure login, registration, and profile management.
Employee Management: Add, view, update, and delete employee records.
Event Management: Create, view, update, and delete company events.
Formation (Training) Management: Manage training sessions, including adding, viewing, updating, and deleting formations.
Project Management: Create and manage projects, assign teams, and track progress.
Team Management: Form and manage teams for various projects.
Reservation Management: Allow employees to reserve spots in training sessions.
Reclamation (Complaint) Management: Submit and manage complaints.
Offer (Job Offer) Management: Post and manage job offers.
Entretien (Interview) Management: Schedule and manage interviews.
Feedback Management: Collect and manage feedback on interviews.

Advanced Features

Facial Recognition: Utilizes AI to provide quick and secure user authentication.
Google OAuth Integration: Allows users to log in using their Google accounts for simplified access.
User Banning and Management: Includes functionality to ban users and manage ban requests.
AI-Powered Sponsor Suggestions: Uses machine learning to suggest potential sponsors based on event types.
Cloud Storage Integration: Stores project data securely in the cloud for easy access and collaboration.
Online Meeting Integration: Facilitates remote training sessions through integrated online meeting tools.
AI-Based CV Filtering: Employs AI algorithms to filter and rank job applications based on CV content.

Installation
To set up the Worksphere application locally, follow these steps:

Clone the repository:
git clone https://github.com/yourusername/worksphere.git

Navigate to the project directory:
cd worksphere

Install backend dependencies:
composer install

Install PHP: Ensure PHP is installed on your system. Download it from php.net if needed.

Set up the database:

Edit the .env file to configure your database settings. For example:DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"

Run the following commands to create the database and apply migrations:php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

Run the Symfony server:
symfony server:start

Usage
Once the application is running:

Open your web browser and navigate to http://localhost:8000.
Log in using your credentials or register a new account.
Use the dashboard to access various management features:
Employee Management: Add, view, update, and delete employee records.
Event Management: Create, view, update, and delete company events.
Formation Management: Manage training sessions.
Project Management: Create and manage projects, assign teams, and track progress.
Reservation Management: Reserve spots in training sessions.
Reclamation Management: Submit and manage complaints.
Offer Management: Post and manage job offers.
Entretien Management: Schedule and manage interviews.
Feedback Management: Collect and manage feedback on interviews.

Contributing
We welcome contributions to improve Worksphere. To contribute:

Fork the repository.
Create a new branch for your feature:git checkout -b feature-name

Commit your changes:git commit -m 'Add some feature'

Push to the branch:git push origin feature-name

Open a pull request.

Please ensure your code adheres to the project's coding standards and includes appropriate tests.
License
This project is licensed under the MIT License - see the LICENSE file for details.
Contact
For any inquiries or support, please contact:

Yassine Bouras - GitHub
Eya Kassous - GitHub
Houssem Hbeib - GitHub
Molka Gharbi - GitHub
Jacem Jouili - GitHub
Asma Sellami - GitHub

Technologies Used

Symfony (Backend)
MySQL (Database)
JavaScript, HTML, CSS (Frontend)
AI and Python Integration

Acknowledgements
This project was developed as part of the PIDEV 3A course at Esprit School of Engineering. We would like to thank our professors and mentors for their guidance and support throughout the development process.
