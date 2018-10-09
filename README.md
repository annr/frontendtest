# Check Your Web

https://frontendtest.com/ checks web pages and web apps for valid markup, broken links, a11y requirements, and web performance best practices...etc.

Note: this is an old, salvaged project written in PHP and it's still PHP.

### TODO

- replace homegrown link checking with https://validator.w3.org/checklink if possible
- replace homegrown a11y test with a service, or develop a11y tests
- update all of app.js to use jQuery and clean up that code
- add small w3c icon to the Nu Validator results
- confirm requests to APIs break gracefully
- Add value to the old best practices descriptions
- add HTML fixtures and local unit tests
- update frontendtest.com WordPress and delete spam comments

### Rules to be added or improved:

- Add details about adding ARIA roles to suspicious class and ID name check
- add check for stale copyright year
- Explain how to add character encoding if it is missing
- make sure font size is large and visible enough

<!-- PS. I'm looking for help working on some open source testing tools. If you are interested in contributing to that kind of thing, please ping me. -->