<?php

namespace App\Controller;

use App\Repositories\OrderRepository;
use App\Resolver\AttributeItemType;
use Doctrine\ORM\EntityManager;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;
use App\Resolver\TypeRegistry;
use App\Repositories\ProductRepository;
use App\Repositories\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entities\OrderItem;
use GraphQL\Error\FormattedError;
// use GraphQL\GraphQL;

class GraphQL {

    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }


    public function handle() {
        try {
            $typeRegistry = new TypeRegistry();
            // Repositories
            $productRepo = new ProductRepository($this->entityManager);
            $categoryRepo = new CategoryRepository($this->entityManager);
            $orderRepo = new OrderRepository($this->entityManager);
            // $products=$productRepo->all();
            // foreach ($products as $product) {
            //     var_dump([
            //         'id' => $product->getId(),
            //         'name' => $product->getName(),
            //         'prices' => $product->getPrices(),
            //     ]);
            // }
            // Define the Query Type
            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'categories' => [
                        'type' => Type::listOf($typeRegistry->get('Category')),
                        'resolve' => static function ($root, $args) use ($categoryRepo) {
                            // error_log(print_r($categoryRepo->all(), true));
                            // var_dump($categoryRepo->all());
                            // error_log(print_r($categoryRepo->find('tech'), true));

                            // return $categoryRepo->find('tech');

                            $categories = $categoryRepo->all(); // Example fetch
                            // error_log(print_r($categories, true));
                            // Ensure $categories is an iterable, such as an array
                            // if (!is_iterable($categories)) {
                            //      throw new RuntimeException('Categories resolver did not return an iterable');
                            // }
                    
                            return $categories;
                        },
                    ],
                    'category' => [
                        'type' => $typeRegistry->get('Category'),
                        'args' => [
                            'id' => Type::nonNull(Type::id()), // Expect an ID argument
                        ],
                        'resolve' => static function ($root, $args) use ($categoryRepo) {
                            // Find a specific category by ID
                            return $categoryRepo->find($args['id']);
                        },
                    ],
                    'products' => [
                        'type' => Type::listOf($typeRegistry->get('Product')),
                        'resolve' => static function ($root, $args) use ($productRepo) {
                            return $productRepo->all();
                        },
                    ],
                    'orders' => [
                        'type' => Type::listOf($typeRegistry->get('Order')),
                        'resolve' => static function () use ($orderRepo) {
                            return $orderRepo->all(); // Return all orders
                        },
                    ],
                    'order' => [
                        'type' => $typeRegistry->get('Order'),
                        'args' => [
                            'id' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => static function ($root, $args) use ($orderRepo) {
                            return $orderRepo->find($args['id']);
                        },
                    ],
                ],
            ]);

            // Define the Mutation Type
            $mutationType = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'createProduct' => [
                        'type' => $typeRegistry->get('Product'),
                        'args' => [
                            'input' => ['type' => Type::nonNull(Type::listOf($typeRegistry->get('AttributeItem')))],
                            'name' => ['type' => Type::nonNull(Type::string())],
                            'isStock' => ['type' => Type::nonNull(Type::boolean())],
                            'gallery' => ['type' => Type::listOf(Type::string())],
                            'description' => ['type' => Type::string()],
                            'categoryId' => ['type' => Type::nonNull(Type::id())],
                            'brand' => ['type' => Type::nonNull(Type::string())],
                            'prices' => ['type' => Type::listOf($typeRegistry->get('Price'))],
                        ],
                        'resolve' => static function ($root, array $args) use ($productRepo, $categoryRepo) {
                            $category = $categoryRepo->find($args['categoryId']);
                            $attributes = $args['input'];
                            $prices = $args['prices'];

                            $product = $productRepo->create([
                                'name' => $args['name'],
                                'isStock' => $args['isStock'],
                                'gallery' => $args['gallery'],
                                'description' => $args['description'],
                                'category' => $category,
                                'attributes' => $attributes,
                                'brand' => $args['brand'],
                                'prices' => $prices,
                            ]);

                            return $product;
                        },
                    ],
                    'createCategory' => [
                        'type' => $typeRegistry->get('Category'),
                        'args' => [
                            'input' => ['type' => Type::nonNull(Type::string())],
                        ],
                        'resolve' => static function ($root, array $args) use ($categoryRepo) {
                            return $categoryRepo->create($args['input']);
                        },
                    ],

                    'createOrder' => [
                        'type' => $typeRegistry->get('Order'),
                        'args' => [
                            'input' => ['type' => Type::nonNull($typeRegistry->get('OrderInput'))],
                        ],
                        'resolve' => static function ($root, $args) use ($orderRepo) {
                            // Ensure you access the input argument correctly
                            $orderData = $args['input'];
                            $items = $orderData['items']; // OrderItemInput[]
                            $order = $orderRepo->create([
                                'id' => $orderData['id']??null,
                                'total_amount' => $orderData['total_amount'],
                                'items' => $items,
                            ]);
                            return $order;
                        },
                    ],
                ],
            ]);

            // Create the schema using SchemaConfig
            $schema = new Schema((new SchemaConfig())
            ->setQuery(new ObjectType($queryType->config))
            ->setMutation(new ObjectType($mutationType->config))
        );
        
        $rawInput = file_get_contents('php://input');
        if ($rawInput === false) {
            throw new RuntimeException('Failed to get php://input');
        }
        
        // error_log('Raw Input: ' . $rawInput);
        
        $input = json_decode($rawInput, true);
        
        $query = $input['query'] ?? null;
        $variableValues = $input['variables'] ?? null;
        
        if (!$query) {
            throw new RuntimeException('GraphQL query is missing.');
        }
        
        $rootValue = ['prefix' => 'You said: '];
        
        try {
            // Use the base GraphQL class
            $result = GraphQLBase::executeQuery($schema, $query, $rootValue, null, $variableValues);
            $output = $result->toArray(true); // Enable error details in response        
        } catch (\Throwable $e) {
            // Log error details
            error_log('GraphQL Execution Error: ' . $e->getMessage());
            error_log('Stack Trace: ' . $e->getTraceAsString());
        
            // Respond with a clean error
            header('Content-Type: application/json', true, 500);

            echo json_encode([
                'errors' => [
                    [
                        'message' => 'An unexpected error occurred.',
                        'details' => $e->getMessage(), // Include error message
                    ],
                ],
            ]);
        }
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}
