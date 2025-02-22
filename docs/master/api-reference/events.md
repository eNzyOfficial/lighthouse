# Events

All events reside in the namespace `\Nuwave\Lighthouse\Events`.

## Lifecycle Events

Lighthouse dispatches the following order of events during a request.

### StartRequest

```php
<?php

namespace Nuwave\Lighthouse\Events;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * Fires right after a request reaches the GraphQLController.
 *
 * Can be used for logging or for measuring and monitoring
 * the time a request takes to resolve.
 *
 * @see \Nuwave\Lighthouse\Support\Http\Controllers\GraphQLController
 */
class StartRequest
{
    /**
     * The request sent from the client.
     *
     * @var \Illuminate\Http\Request
     */
    public $request;

    /**
     * The point in time when the request started.
     *
     * @var \Illuminate\Support\Carbon
     */
    public $moment;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->moment = Carbon::now();
    }
}
```

### StartOperationOrOperations

```php
<?php

namespace Nuwave\Lighthouse\Events;

/**
 * Fires after receiving the parsed operation (single query) or operations (batched query).
 */
class StartOperationOrOperations
{
    /**
     * One or multiple parsed GraphQL operations.
     *
     * @var \GraphQL\Server\OperationParams|array<int, \GraphQL\Server\OperationParams>
     */
    public $operationOrOperations;

    /**
     * @param  \GraphQL\Server\OperationParams|array<int, \GraphQL\Server\OperationParams>  $operationOrOperations
     */
    public function __construct($operationOrOperations)
    {
        $this->operationOrOperations = $operationOrOperations;
    }
}
```

### BuildSchemaString

```php
<?php

namespace Nuwave\Lighthouse\Events;

/**
 * Fires before building the AST from the user-defined schema string.
 *
 * Listeners may return a schema string,
 * which is added to the user schema.
 *
 * Only fires once if schema caching is active.
 */
class BuildSchemaString
{
    /**
     * The root schema that was defined by the user.
     *
     * @var string
     */
    public $userSchema;

    public function __construct(string $userSchema)
    {
        $this->userSchema = $userSchema;
    }
}
```

### RegisterDirectiveNamespaces

```php
<?php

namespace Nuwave\Lighthouse\Events;

/**
 * Fires when the directive factory is constructed.
 *
 * Listeners may return one or more strings that are used as the base
 * namespace for locating directives.
 *
 * @see \Nuwave\Lighthouse\Schema\DirectiveLocator::namespaces()
 */
class RegisterDirectiveNamespaces
{
    //
}
```

### ManipulateAST

```php
<?php

namespace Nuwave\Lighthouse\Events;

use Nuwave\Lighthouse\Schema\AST\DocumentAST;

/**
 * Fires after the AST was built but before the executable schema is built.
 *
 * Listeners may mutate the $documentAST and make programmatic
 * changes to the schema.
 *
 * Only fires once if schema caching is active.
 */
class ManipulateAST
{
    /**
     * The AST that can be manipulated.
     *
     * @var \Nuwave\Lighthouse\Schema\AST\DocumentAST
     */
    public $documentAST;

    public function __construct(DocumentAST &$documentAST)
    {
        $this->documentAST = $documentAST;
    }
}
```

### StartExecution

```php
<?php

namespace Nuwave\Lighthouse\Events;

use GraphQL\Language\AST\DocumentNode;
use Illuminate\Support\Carbon;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Fires right before resolving a single operation.
 *
 * Might happen multiple times in a single request if batching is used.
 */
class StartExecution
{
    /**
     * The parsed schema.
     *
     * @var \GraphQL\Type\Schema;
     */
    public $schema;

    /**
     * The client given parsed query string.
     *
     * @var \GraphQL\Language\AST\DocumentNode
     */
    public $query;

    /**
     * The client given variables, neither validated nor transformed.
     *
     * @var array<string, mixed>|null
     */
    public $variables;

    /**
     * The client given operation name.
     *
     * @var string|null
     */
    public $operationName;

    /**
     * The context for the operation.
     *
     * @var \Nuwave\Lighthouse\Support\Contracts\GraphQLContext
     */
    public $context;

    /**
     * The point in time when the query execution started.
     *
     * @var \Illuminate\Support\Carbon
     */
    public $moment;

    /**
     * @param array<string, mixed>|null $variables
     */
    public function __construct(Schema $schema, DocumentNode $query, ?array $variables, ?string $operationName, GraphQLContext $context)
    {
        $this->schema = $schema;
        $this->query = $query;
        $this->variables = $variables;
        $this->operationName = $operationName;
        $this->context = $context;
        $this->moment = Carbon::now();
    }
}
```

### BuildExtensionsResponse

```php
<?php

namespace Nuwave\Lighthouse\Events;

/**
 * Fires after a query was resolved.
 *
 * Listeners may return a @see \Nuwave\Lighthouse\Execution\ExtensionsResponse
 * to include in the response.
 */
class BuildExtensionsResponse
{
    //
}
```

```php
<?php

namespace Nuwave\Lighthouse\Execution;

/**
 * May be returned from listeners of @see \Nuwave\Lighthouse\Events\BuildExtensionsResponse
 */
class ExtensionsResponse
{
    /**
     * Will be used as the key in the response map.
     *
     * @var string
     */
    protected $key;

    /**
     * @var mixed JSON-encodable content of the extension.
     */
    protected $content;

    /**
     * @param  mixed  $content JSON-encodable content
     */
    public function __construct(string $key, $content)
    {
        $this->key = $key;
        $this->content = $content;
    }

    /**
     * Return the key of the extension.
     */
    public function key(): string
    {
        return $this->key;
    }

    /**
     * @return mixed JSON-encodable content of the extension.
     */
    public function content()
    {
        return $this->content;
    }
}
```

### ManipulateResult

```php
<?php

namespace Nuwave\Lighthouse\Events;

use GraphQL\Executor\ExecutionResult;

/**
 * Fires after resolving each individual query.
 *
 * This gives listeners an easy way to manipulate the query
 * result without worrying about batched execution.
 */
class ManipulateResult
{
    /**
     * The result of resolving an individual query.
     *
     * @var \GraphQL\Executor\ExecutionResult
     */
    public $result;

    public function __construct(ExecutionResult &$result)
    {
        $this->result = $result;
    }
}
```

### EndExecution

```php
<?php

namespace Nuwave\Lighthouse\Events;

use GraphQL\Executor\ExecutionResult;
use Illuminate\Support\Carbon;

/**
 * Fires after resolving a single operation.
 */
class EndExecution
{
    /**
     * The result of resolving a single operation.
     *
     * @var \GraphQL\Executor\ExecutionResult
     */
    public $result;

    /**
     * The point in time when the result was ready.
     *
     * @var \Illuminate\Support\Carbon
     */
    public $moment;

    public function __construct(ExecutionResult $result)
    {
        $this->result = $result;
        $this->moment = Carbon::now();
    }
}
```

### EndOperationOrOperations

```php
<?php

namespace Nuwave\Lighthouse\Events;

/**
 * Fires after resolving a single or multiple operations.
 */
class EndOperationOrOperations
{
    /**
     * Single or multiple operation results.
     *
     * @var array<string, mixed>|array<int, array<string, mixed>>
     */
    public $results;

    /**
     * @param  array<string, mixed>|array<int, array<string, mixed>>  $results
     */
    public function __construct(array $results)
    {
        $this->results = $results;
    }
}
```

### EndRequest

```php
<?php

namespace Nuwave\Lighthouse\Events;

use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

/**
 * Fires right after building the HTTP response in the GraphQLController.
 *
 * Can be used for logging or for measuring and monitoring
 * the time a request takes to resolve.
 *
 * @see \Nuwave\Lighthouse\Support\Http\Controllers\GraphQLController
 */
class EndRequest
{
    /**
     * The response that is about to be sent to the client.
     *
     * @var \Symfony\Component\HttpFoundation\Response
     */
    public $response;

    /**
     * The point in time when the response was ready.
     *
     * @var \Illuminate\Support\Carbon
     */
    public $moment;

    public function __construct(Response $response)
    {
        $this->response = $response;
        $this->moment = Carbon::now();
    }
}
```

## Non-lifecycle Events

The following events happen outside of the execution lifecycle.

### ValidateSchema

```php
<?php

namespace Nuwave\Lighthouse\Events;

use GraphQL\Type\Schema;

/**
 * Dispatched when php artisan lighthouse:validate-schema is called.
 *
 * Listeners should throw a descriptive error if the schema is wrong.
 */
class ValidateSchema
{
    /**
     * The final schema to validate.
     *
     * @var \GraphQL\Type\Schema
     */
    public $schema;

    public function __construct(Schema $schema)
    {
        $this->schema = $schema;
    }
}
```
