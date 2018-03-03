Register the bundle as usual and mount its routing. 

In your config.yml

```yaml
forci_catchable:
    entity_manager: default
```

Where `entity_manager` is your SEPARATE entity manager.

For this bundle to make any sense at all, you need to create a separate connection and a separate entity manager.

The above configuration uses the default one and WILL NOT WORK in case you have an exception thrown by Doctrine, as it will also close the entity manager which is responsible for writing it to the database.

WARNING: This bundle is NOT meant to be a replacement for your file or else logging. It is meant to work alongside your existing infrastructure and make viewing errors easier and nicer.

In your config_prod.yml

```yaml
monolog:
    handlers:
        limitless:
            type: service
            id: forci.catchable.handler.limitless_buffer
```

This handler will collect all the logs and store them in an array, which will be available for Catchable's subscriber and saved to the database Exception entry.

This also makes it possible for you to use the logger and log critical data that may be important in case of a disaster.

Then, make a link somewhere in your app to

```twig
<a href="{{ path('forci_catchable_root') }}">
    Errors
</a>
```

Enjoy!