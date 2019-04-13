# MBT Examples Bundle [![Build Status][travis_badge]][travis_link]

This Bundle contains examples code for MBT Bundle.

## Development

### Generate code
```
$ tests/app/bin/console make:generator name ClassName
$ tests/app/bin/console make:subject name ClassName
$ tests/app/bin/console make:reducer name ClassName
```

### Dump model
```
$ tests/app/bin/console workflow:dump checkout > src/Resources/config/models/checkout/checkout.dot
$ tests/app/bin/console workflow:dump checkout --dump-format=puml > src/Resources/config/models/checkout/checkout.plantuml
$ cat src/Resources/config/models/checkout/checkout.dot | dot -Tpng -o src/Resources/config/models/checkout/checkout.png
$ cat src/Resources/config/models/checkout/checkout.plantuml | java -jar ~/Programs/plantuml.jar -p > src/Resources/config/models/checkout/checkout.puml.png
```

### Clear cache
```
$ tests/app/bin/console cache:clear
```

## License

This package is available under the [MIT license](LICENSE).

[travis_badge]: https://travis-ci.org/tienvx/mbt-examples-bundle.svg?branch=master
[travis_link]: https://travis-ci.org/tienvx/mbt-examples-bundle
