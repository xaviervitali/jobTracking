# JobTracking

JobTracking is a Symfony-based application designed to help job seekers efficiently manage their job applications. This tool allows users to track job offers, manage ongoing job actions, and get an overview of their job search progress through a dedicated dashboard.

## Features

- **Job Application Tracking**: Easily store and organize information about job offers, applications, and their current status.
- **Dashboard Overview**: Visual graphs and statistics on your applications to help you monitor your progress.
- **Action Management**: Stay on top of your job search by adding tasks, reminders, and actions related to specific applications.
- **Resume Upload**: Upload and store your CV for easy access and retrieval.
- **API Integration**: Retrieve job offers directly from external sources, like Adzuna or France Travail, using their respective APIs.

## Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/xaviervitali/jobTracking.git
    ```
2. Navigate into the project directory:
    ```bash
    cd jobTracking
    ```
3. Install dependencies using Composer:
    ```bash
   composer install
    ```
4. Set up the database:
    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```
5. Start the server:
    ```bash
    symfony server:start
     ```
6. Open your browser and visit http://localhost:8000.

## Configuration
Copy .env.example to .env and adjust your database and API configurations:
- Copy .env.example to .env and adjust your database and API configurations:
     ```bash
     cp .env.example .env
     ```
- Set your Adzuna API settings or France Travail API tokens in your .env file or database.

## Technologies Used
- **Symfony**: PHP framework for web applications
- **Bootstrap**: CSS framework for a responsive design
- **JavaScript (JQuery)**: For enhanced front-end interactivity
- **Apexcharts**: For visualizing application data
- **MySQL**: Database management system
- **Adzuna API**: For fetching job offers
- **France Travail API**: For job search integration

## Future Improvements
- Implement user roles.
- Expand the range of integrated job offer APIs.
- Enable automatic follow-up reminders for job applications.


   
