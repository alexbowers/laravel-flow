## Laravel Flow

**Package in development**

Laravel Flow is an improvement to the way that Queues work in Laravel.

Laravel Flow allows you to schedule queue jobs to happen in the distant future on any queue driver (whereas Laravel itself is restricted on a per driver basis).

A queue can then be executed a specified number of times, until a specific condition is matched, and repeatedly delayed until conditions are favourable.

A flow can be setup automatically based on the Eloquent events `created`, `updated`, `retrieved`, `saved`, `deleted`, and `restored`.

A flow can also be setup on any custom events (class based or string based) you wish.

### Documentation

This package is currently largely documented by its tests.

The documentation below may be an incomplete representation of the feature set of the package.

---

- Running a flow immediately
    Running a flow immediately has few benefits over the traditional Laravel Queue setup, except that a logged record of the task is kept, which may be depended upon for future flows to occur.
    
- Delaying a flow by X days
    You can delay a flow using the `delay` function. This function should return a carbon date instance of the date the flow should be made available, or it should return a carbon interval instance.
