Register the bundle as usual and mount its routing. 

For this bundle to make any sense at all, you need to create a separate connection and a separate entity manager.
From version 0.7 and up, you no longer need any config for this bundle.
Follow https://symfony.com/doc/4.4/doctrine/multiple_entity_managers.html and map `ForciCatchableBundle` to your separate EM.

Better yet, create a completely separate database. Having a separate entity manager will also mean you will NOT be able to migrate this with `DoctrineMigrationsBundle` as it does NOT support multiple entity managers (at least to my knowledge). So create/update your separate database as per your project's deployment strategy and/or needs.

The above configuration uses the default one and WILL NOT WORK in case you have an exception thrown by Doctrine, as it will also close the entity manager which is responsible for writing it to the database.

WARNING: This bundle is NOT meant to be a replacement for your file or else logging. It is meant to work alongside your existing infrastructure and make viewing errors easier and nicer via your admin interface.

## Versions >= ~0.6

In your config_prod.yml

```yaml
monolog:
    handlers:
        main:
            type:         fingers_crossed
            action_level: critical
            handler:      grouped
            # 404 and 405 can build up tons if unnecessary data
            # You may be better off tracking those from your web server's logs
            excluded_http_codes: [404, 405]
            # Alternatively, exclude just 404s at given paths
#            excluded_404s:
#                # regex: exclude all 404 errors from the logs
#                - ^/
        grouped:
            type:    group
            members: [streamed, buffered, catchable]
        # Log errors to a file
        streamed:
            type:  stream
            path:  "%kernel.logs_dir%/%kernel.environment%.log"
            level: debug
            include_stacktraces: true
        # Buffer errors to be sent via swift mailer
        buffered:
            type:    buffer
            handler: swift
        # Actual swift mailer handler that gets invoked when action_level: critical
        # from the main handler occurs
        swift:
            type:       swift_mailer
            from_email: errors@some-domain.com
            to_email:   "dev1@some-domain.com"
            # or list of recipients
            # to_email:   [dev1@some-domain.com, dev2@some-domain.com, ...]
            subject:    "[My Project] An Error in PROD Occurred!"
            level:      debug
            include_stacktraces: true
        # CatchableBundle's buffer handler. Logs are formatted using the hardcoded \Monolog\Formatter\ScalarFormatter
        # and stored locally in an array. After that, upon an Exception, the `Forci\Bundle\Catchable\Subscriber\ExceptionSubscriber`
        # is invoked, and fetches the logs from this buffer handler. This allows you to have your Symfony logs persisted
        # together with the serialized \Throwable instance.
        catchable:
            type: service
            id: forci.catchable.monolog.handler.log_buffer
```

You could also pass Catchable's logs through a FilterHandler to get rid of bloat such as deprecations.
This allows you to only have critical error information persisted, but deprecations logged to a file by the remaining handlers.
Either use accepted_levels or min_level / max_level, whichever suits your needs best.
Just be careful if allowing deprecations to be persisted. Upon many 404 errors, you'll get the database fill up quickly with redundant data.
In the future, functionality like ignored errors (such as the aforementioned HttpNotFoundException) will be added.

```yaml
        catchable:
            type: filter
            handler: catchable_real
            # Either
#            min_level: debug
#            max_level: emergency
            # Or
            accepted_levels: [debug, critical]
        catchable_real:
            type: service
            id: forci.catchable.monolog.handler.log_buffer
```

Alternatively, instead of fingers_crossed, you could use a filter or buffer or any other handler.
Please note: When using buffer, it must flush at some point. This must also happen BEFORE the ExceptionSubscriber is fired.
As it is, using it alongside your fingers_crossed file and/or email logging is the best approach.
Other handlers haven't been tested with Catchable's buffer service. Use at your own risk.

```yaml
monolog:
    handlers:
        # Filter logs to a level you'd like
        filter:
            type: filter
            level: debug
            handler: catchable
        # Then channel them into Catchable's buffer for use by the ExceptionSubscriber as described above
        catchable:
            type: service
            id: forci.catchable.monolog.handler.log_buffer
```

## Versions <= ~0.5
In your config.yml

```yaml
forci_catchable:
    entity_manager: default
```

Where `entity_manager` is your SEPARATE entity manager.

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

## Release 0.6

- Improved buffer handler. You can now configure it in your main chain of handlers.
- Removed deprecations. Allows Symfony 5 compatibility.
- Now uses `FlattenException` from Symfony's `ErrorHandler` component, rather than `Debug`.
- Removed hack around serializing `\Throwable` via the deprecated `FatalThrowableError` class.

## Upgrade 0.5 -> 0.6

- Symfony requirements are now ~4.4|~5.0
- The `Forci\Bundle\Catchable\Serializer\ExceptionSerializer` service is removed. All of its code are now in `Forci\Bundle\Catchable\Collector\ThrowableCollector`
- The `forci.catchable.handler.limitless_buffer` service has been removed in favor of a simpler `forci.catchable.monolog.handler.log_buffer`
- Your buffering and/or filtering logic must now be handled by any of the built-in Monolog handlers. This makes buffering relevant logs more streamlined and in line with your other handlers.

--

TODOs

- Make it possible to ignore an exception class
- Add a "Hide User Deprecated" checkbox that hides log messages starting with "User Deprecated"
- Add file and message hashes, search by hash instead for better performance as these are text and have no indexes (Blame Doctrine for not allowing to specify index length)
- Rename to ExceptionBundle, ExceptionCollector, ExceptionSerializer, etc
- Add filter exceptions config
- Hash Error type, file, line, message, current DATE/HOUR and increment an "occurrences" counter @ Catchable Entity