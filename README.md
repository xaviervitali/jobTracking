JobTracking
JobTracking is a Symfony-based application designed to help job seekers efficiently manage their job applications. This tool allows users to track job offers, manage ongoing job actions, and get an overview of their job search progress through a dedicated dashboard.

Features
Job Application Tracking: Easily store and organize information about job offers, applications, and their current status.
Dashboard Overview: Visual graphs and statistics on your applications to help you monitor your progress.
Action Management: Stay on top of your job search by adding tasks, reminders, and actions related to specific applications.
Resume Upload: Upload and store your CV for easy access and retrieval.
API Integration: Retrieve job offers directly from external sources, like Adzuna or France Travail, using their respective APIs.
Installation
Clone the repository:

git clone https://github.com/xaviervitali/jobTracking.git
Navigate into the project directory:



cd jobTracking
Install dependencies using Composer:


composer install
Set up the database:

php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
Start the server:

symfony server:start
Open your browser and visit http://localhost:8000.

Copy .env.example to .env and adjust your database and API configurations:

cp .env.example .env
Set your Adzuna API settings or France Travail API tokens in your .env file or database.

Symfony: PHP framework for web applications
Bootstrap: CSS framework for a responsive design
JavaScript (JQuery): For enhanced front-end interactivity
Apexchart.js: For visualizing application data
MySQL: Database management system
Adzuna API: For fetching job offers
France Travail API: For job search integration

Future Improvements
Expand the range of integrated job offer APIs.
Enable automatic follow-up reminders for job applications.
