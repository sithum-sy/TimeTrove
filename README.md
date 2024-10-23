# TimeTrove Scheduler

TimeTrove Scheduler is a Laravel-based appointment scheduling application designed to simplify and streamline appointment management for various service providers. The platform helps businesses efficiently manage appointments, communicate with clients, and reduce no-show rates through automated reminders. TimeTrove supports location-based filtering to match clients with local service providers, promoting convenience and supporting community-based services.

## Features

-   **Appointment Scheduling:** Easily schedule appointments with service providers.
-   **Location-Based Filtering:** Match clients with nearby service providers based on their location.
-   **Automated Reminders:** Reduce no-shows with email or SMS reminders.
-   **Calendar View:** Visualize upcoming appointments for better schedule management.
-   **Client Information Management:** Manage client details and appointment history for personalized services.
-   **Secure Communication:** Communicate securely with service providers through the platform.
-   **Feedback and Ratings:** Collect client feedback after services are completed.

## Getting Started

Follow these steps to set up TimeTrove on your local environment.

### Prerequisites

-   PHP 8.0 or higher
-   Composer
-   Laravel 9.x or higher
-   MySQL or any other supported database
-   Node.js and npm (for frontend dependencies)

### Installation

1.  **Clone the repository:**

    ```bash
    git clone https://github.com/your-username/timetrove-scheduler.git
    cd timetrove-scheduler
    ```

2.  **Install dependencies:**
    Run the following command to install the PHP dependencies:

    ```bash
    composer install
    ```

    Then, install the frontend dependencies:

    ```bash
    npm install
    ```

3.  **Environment setup:**
    Copy the .env.example file to .env:

    ```bash
    cp .env.example .env
    ```

4.  **Generate the application key:**

    ```bash
    php artisan key:generate
    ```

5.  **Run the database migrations:**

    ```bash
    php artisan migrate
    ```

6.  **Seed the database:**

    ```bash
    php artisan db:seed
    ```

7.  **Start the development server:**

    ```bash
    php artisan serve
    ```

8.  **Compile frontend assets:**

    ```bash
    npm run dev
    ```

### Accessing the Application

Visit http://localhost:8000 in your browser to access the TimeTrove Scheduler.

### Contributing

Feel free to submit issues or pull requests if you would like to contribute to the project. Please ensure that your contributions are well-documented and follow the code standards of the project.

### License

This project is licensed under the MIT License. See the LICENSE file for more details.
