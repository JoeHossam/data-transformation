# Data Transformation

This project is about sending data to a third party application after converting it to the required schema by this app.  
It takes in the resource ID (`claim_id`) and schema ID (`endpoint_id`) and transforms the resource's data to this specific schema and return it.

## Features

-   Configurable mapping rules stored in the database
-   Support for complex and nested data types: attributes, objects, arrays, and object lists
-   Logging and custom error handling
-   Extensible design for adding new data types and collections
-   Caching requests for improved performance

## Mapping rules assumptions

-   The `internal_field` is the field inside our own database
    -   case1: `[fieldName]` will refer to a column inside the `claim` table
    -   case2: `[tableName].[fieldName]` will refer to a column inside a table that belongs to the claim.
-   The rule with an `object_list` type will always return a collection (having the collection name as the `internal_field`) and any children to that rule will act as the column names inside this collection.

## Prerequisites

-   PHP 8.0 or higher
-   Composer
-   Laravel 8.11

## Installation

1. clone the repository.

```
git clone https://github.com/JoeHossam/data-transformation
```

2. make sure you're inside the project directory and install the dependecies

```
composer install
```

3. Run the database migrations

```
php artisan migrate
```

4. Seed the database

```
php artisan db:seed
```

5. Start the development server

```
php artisan serve
```

The application will be available at `http://localhost:8000` and make sure form the console.

## Usage

you can start making request to `POST /api/general/external-integration`

Expectes body

```JSON
{
    "claim_id": 1,
    "endpoint_id": 22
}
```

-   `claim_id` is the resource ID.
-   `endpoint_id` will apply the rules that have this endpoint ID

Expected response is based on the schema found in the DB  
Example

```JSON
{
    "claimReference": "CLM-8603-zuxf",
    "payer": {
        "payerName": "Kreiger-Wuckert",
        "payerPhone": "1-747-726-8812"
    },
    "notes": [
        "Repellendus e...",
        "Error...."
    ],
    "claimStatuses": [
        {
            "date": "2024-01-21 02:28:41",
            "status": "approved"
        },
        {
            "date": "2024-04-10 09:55:04",
            "status": "completed"
        },
        {
            "date": "2024-05-07 08:10:24",
            "status": "approved"
        }
    ]
}
```
