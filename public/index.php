<?php

// Include Composer's autoloader
require_once __DIR__ . '/../vendor/autoload.php';

use Doctrine\ORM\Tools\Console\ConsoleRunner;

use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;  // Import SchemaTool for schema creation
use Dotenv\Dotenv;
use FastRoute\RouteCollector;
use FastRoute\Dispatcher;
use App\Controller\GraphQL;

// Load environment variables from .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Database configuration from environment variables
$dbParams = [
    'driver'   => 'pdo_mysql',
    'host'     => $_ENV['DB_HOST'],
    'port'     => $_ENV['DB_PORT'],
    'dbname'   => $_ENV['DB_NAME'],
    'user'     => $_ENV['DB_USER'],
    'password' => $_ENV['DB_PASS'],
];

// Entity paths (make sure these paths point to your entities)
$entityPaths = [__DIR__ . '/../src/Entities'];
$isDevMode = true;  // Set to false in production

// Set up Doctrine ORM configuration
$config = ORMSetup::createAttributeMetadataConfiguration(
    $entityPaths, 
    $isDevMode
);

// Create the database connection using DBAL
$connection = DriverManager::getConnection($dbParams, $config);

// Create the EntityManager
try {
    $entityManager = new EntityManager($connection, $config);
} catch (\Exception $e) {
    echo "Error creating EntityManager: " . $e->getMessage();
    exit;
}

// Fetch metadata for all entities
// $metadata = $entityManager->getMetadataFactory()->getAllMetadata();
// var_dump($metadata);
// // If metadata is empty, display an error
// if (empty($metadata)) {
//     echo "No entities found.\n";
//     exit;
// }

// Create SchemaTool to handle the schema operations
// $schemaTool = new SchemaTool($entityManager);

// try {
//     $schemaTool->dropSchema($metadata);

//     $schemaTool->createSchema($metadata); // This will create tables
//     echo "Schema created successfully!";
// } catch (\Exception $e) {
//     echo "Error creating schema: " . $e->getMessage();
// }

// Set up FastRoute dispatcher
$dispatcher = FastRoute\simpleDispatcher(function (RouteCollector $r) {
    // Define your routes here
    $r->addRoute('POST', '/graphql', [GraphQL::class, 'handle']);
});

header('Access-Control-Allow-Origin: http://localhost:5173');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

// Handle preflight (OPTIONS) requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Fetch the request method and URI
$routeInfo = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);

// Route handling based on dispatch result
switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo json_encode(['error' => 'Not Found']);
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed', 'allowedMethods' => $allowedMethods]);
        break;

    case Dispatcher::FOUND:
        // Route matched, handle the request
        $handler = $routeInfo[1];
        $vars = $routeInfo[2];

        // Instantiate GraphQL Controller and handle the request
        if ($handler == [GraphQL::class, 'handle']) {
            if (!$entityManager) {
                echo json_encode(['error' => 'EntityManager could not be initialized.']);
                return;
            }
            $graphqlController = new GraphQL($entityManager);
            echo $graphqlController->handle();
        }
        break;
}
