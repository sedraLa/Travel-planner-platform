# Smart Tourism and Travel Management System

This project is a web-based smart tourism and travel management platform developed using the Laravel framework.
The system aims to centralize travel services such as destination exploration, hotel booking, transportation,
flight searching, secure payments ,and AI-assisted trip planning within a single platform.

## Technologies Used
- Laravel Framework (PHP)
- HTML, CSS, JavaScript
- Tailwind CSS
- MySQL Database
- PayPal Payment Gateway
- OpenStreetMap and Geocoding Services
- OpenWeather API
- Amadeus API (Flight Searching)
- Groq (AI Trip Planning)

## How to Run the Project

### Prerequisites
Before running the project, make sure the following tools are installed:
- PHP (version 8.0 or higher)
- Composer
- Node.js and npm
- MySQL

### Installation Steps

1. Navigate to the project directory:
```bash
cd project-name
```
2. Install PHP dependencies:
```bash
composer install
```
3. Install frontend dependencies:
```bash
npm install
```
4. Create environment configuration file:
```bash
cp .env.example .env
```
5. Generate application key:
```bash
php artisan key:generate
```
## Configure the database settings in the .env file.

7. Run database migrations:
```bash
php artisan migrate
```
8. Run npm :
```bash
npm run dev
```
9. Start the development server:
```bash
php artisan serve
```
The application will be accessible at:
http://127.0.0.1:8000

## Notes:
- Make sure the database is created before running migrations.
- API keys for external services should be added to the .env file.


e](https://opensource.org/licenses/MIT).
