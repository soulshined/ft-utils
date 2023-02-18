
`> composer require ft/utils:dev-master`

## FT\Utils\Optional

A class for working with nullable values.

An implementation emulating Javas `Optional<>`

```mermaid
classDiagram
    class Optional {
        of(mixed $value) Optional
        ofNullable(mixed $value) Optional
        absent() Optional
        get() mixed
        isPresent() bool
        ifPresent(callable $consumer) void
        ifPresentOrElse(callable $consumer, callable $else) void
        orElse(mixed $else) mixed
        orElseThrow(?Throwable $throwable = null) mixed
        map(callable $mapper) Optional
        equals($object) bool
    }
```

See `tests/OptionalTest.php` for usage examples and intentions

## FT\Utils

### Functions
- `last(mixed $value, ?callable $predicate = null, ?int $qty = null) : mixed`

    Retrieve the last component of a given element

    ```php
    Utils::last(['a','b','c']); // 'c'
    Utils::last(['a','b','c'], qty: 2); //['b','c']
    Utils::last(['a val','b val','c val'], fn($i) => str_starts_with($i, 'b'), qty: 2); // ['b val']
    Utils::last([['key' => 'value', 'foo' => 'bar']]); //['foo' => 'bar']
    Utils::last("foobar"); // 'r'
    Utils::last("foobar", 3); // 'bar'

    $class = new class {
        public function __construct(
            public readonly $foo = "bar",
            public readonly $bazz = "buzz"
        )
    }
    Utils::last($class); // ['bazz' => 'buzz']
    ```

- first

    Retrieve the first component of a given element

    ```php
    Utils::first(['a','b','c']); // 'a'
    Utils::first(['a','b','c'], qty: 2); //['a','b']
    Utils::first(['a val','b val','c val'], fn($i) => str_starts_with($i, 'b'), qty: 2); // ['b val']
    Utils::first([['key' => 'value', 'foo' => 'bar']]); //['key' => 'value']
    Utils::first("foobar"); // 'f'
    Utils::first("foobar", 3); // 'foo'

    $class = new class {
        public function __construct(
            public readonly $foo = "bar",
            public readonly $bazz = "buzz"
        )
    }
    Utils::first($class); // ['foo' => 'bar']
    ```

- guidv4

### Arrays
- `flatten(array $haystack, int $depth = 1) : array`

    ```php
    $arr = [[1,2,3],[4,5,6],[7,[8,9]]];
    Utils::$Array::flatten($arr); //[1,2,3,4,5,6,7,[8,9]]
    ```

- `anyMatch(array $haystack, callable $predicate) : bool`

    ```php
    $arr = [1,2,3,4,5]
    Utils::$Array::anyMatch($arr, 'is_int'); //true
    Utils::$Array::anyMatch($arr, 'is_string'); //false
    ```
- `allMatch(array $haystack, callable $predicate) : bool`

    ```php
    $arr = [1,2,3,4,5]
    Utils::$Array::allMatch($arr, 'is_int'); //true
    Utils::$Array::allMatch($arr, fn($i) => $i > 3); //false
    ```
- `noneMatch(array $haystack, callable $predicate) : bool`

    ```php
    $arr = [1,2,3,4,5]
    Utils::$Array::noneMatch($arr, 'is_int'); //false
    Utils::$Array::noneMatch($arr, 'is_string'); //true
    ```
- `assc_array_map(array $haystack, callable $callback): array`

    ```php
    $headers = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ];
    Utils::$Array::assc_array_map($headers, fn ($k, $v) => "$k: $v");
    /*
        ['Content-Type: application/json', 'Accept: application/json']
    */
    ```

- `find_by_property_values(array $haystack, array $pvps) : mixed`

    Returns the first found element that matches the given criteria

    ```php
    class User {
        public function __construct(
            public readonly string $fname,
            public readonly string $lname,
            public readonly int $age,
        )
    }

    $users = [
        new User('john', 'doe', 99),
        new User('jane', 'doe', 77),
        new User('jerry', 'doe', 88)
    ];

    Utils::$Array::find_by_property_values($users, ['lname', 'doe']);
    // object(fname=john,lname=doe,age=99)

    Utils::$Array::find_by_property_values($users, ['lname' => 'doe', 'fname' => 'jerry']);
    // object(fname=jerry,lname=doe,age=88)
    ```

- `sort_by_property(array &$haystack, string $property, string $direction = 'asc')`

    Sorts an array of objects by a property
    ```php
    class User {
        public function __construct(
        public readonly string $fname,
        public readonly string $lname,
        public readonly int $age,
    )
    }

    $users = [
        new User('john', 'doe', 99),
        new User('jane', 'doe', 77),
        new User('jerry', 'doe', 88)
    ];

    Utils::$Array::sort_by_property($users, 'age');
    /*
        [
            object(fname=jane),
            object(fname=jerry),
            object(fname=john)
        ]
    */

    Utils::$Array::sort_by_property($users, 'age', 'desc');
    /*
        [
            object(fname=john),
            object(fname=jerry),
            object(fname=jane)
        ]
    */
    ```

- `sort_by_value_key(array &$haystack, string $key, string $direction = 'asc')`

    Sorts an array of values by a value key

    ```php
    $users = [
        [
            'fname' => 'john',
            'lname' => 'doe',
            'age' => 99
        ],
        [
            'fname' => 'jane',
            'lname' => 'doe',
            'age' => 77
        ],
        [
            'fname' => 'jerry',
            'lname' => 'doe',
            'age' => 88
        ]
    ];

    Utils::$Array::sort_by_value_key($users, 'age');
    /*
        [
            array(fname=jane),
            array(fname=jerry),
            array(fname=john)
        ]
    */

    Utils::$Array::sort_by_value_key($users, 'age', 'desc');
    /*
        [
            array(fname=john),
            array(fname=jerry),
            array(fname=jane)
        ]
    */
    ```

### Path
- `join(string ...$segments): string`

    Join paths using native `DIRECTORY_SEPARATOR` constant

- `scan_for_files(string $directory, string $file_regexp, array $ignore = []): array`

    Scan for files *recursively* in a given directory that match a given pattern. Returns an array of `SplFileInfo`

    This supports ignoring files

    > **Note** The `/` in the ignore patterns do not need to be escaped
    >
    > All paths get converted to a real path and all separators get converted to a forward slash `/`

    ```php
    //return all php files
    Utils::$Path::scan_for_files(__DIR__, "\.php$");
    Utils::$Path::scan_for_files(__DIR__, "\.php$",
        ignore: ['/vendor/?.*']
    );
    ```

- `getFileSystemIteratorFilter`

  This custom FileSystemIterator allows ignoring patterns (ignoring is leaf type agnostic, the pattern will validate against directories and paths)

  FileSystemIterator constructor
    ```php
    public function __construct(
        RecursiveIterator $iterator,
        private readonly array $ignore = []
    ) {}
    ```

  FileSystemIterator example usage
    ```php
    $dir_it = new RecursiveDirectoryIterator(__DIR__, RecursiveDirectoryIterator::SKIP_DOTS);
    $fs_it = Utils::$Path::getFileSystemIteratorFilter($dir_it, $ignore_array);
    ```
### Preg
- `count(string $pattern, string $subject, int $flags = 0, int $offset = 0) : int`

    Returns the number matches for a given a pattern

    This is guaranteed to return an integer

- `match_all(string $pattern, string $subject, &$matches, int $flags = 0, int $offset = 0)`

    Return all matches of a given pattern

    ```php
    $str = <<<'A'
    https://johndoe:pa$$w0rd@example.com

    https://janedoe:pa$$w0rd@example.com
    A;

    Utils::$Regex::match_all('/https:\/\/(?:(?<username>\w+):(?<password>\S+))@example\.com/', $str, $matches);

    $john_match = $matches[0];

    //accessing match metadata
    $john_match->value; // https://johndoe:pa$$w0rd@example.com
    $john_match->start; // 0
    $john_match->end; // 36

    //accessing groups
    $john_match->groups->{'1'}; // johndoe
    $john_match->groups->username; // johndoe
    $john_match->g1; //johndoe

    $john_match->groups->{'2'}; // pa$$w0rd
    $john_match->groups->password; // pa$$w0rd
    $john_match->g2; //pa$$w0rd
    ```
### Strings
- `charAt(string $value, int $index) : ?string`
- `utf8(string $value): ?string`
- `explode_implode(
            string $value,
            string $explode_delimiter,
            ?callable $foreach = null,
            ?string $implode_delimiter = null,
            int $limit = PHP_INT_MAX
        ) : string`

    Utility to quickly explode a string, optionally perform a mapping operation on each value and then implode it back

    > **Note** the foreach map is applied to the exploded operation

    ```php
    $str = "key=value; key2=value2; key3=value3";

    $result = Utils::$String::explode_implode(
        $str,
        explode_delimiter: ";",
        implode_delimiter: " / ",
        foreach: fn($i) =>
            Utils::$String::explode_implode($i, "=", null, " => ")
    );

    echo $result; // "key => value /  key2 => value2 /  key3 => value3"
    ```