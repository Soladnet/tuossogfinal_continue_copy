<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset=utf-8>
        <meta name="viewport" content="width=620">
        <title>HTML5 Demo: Online connectivity monitoring</title>
    <body>
        <section id="wrapper">
            <article>
                <p>Current network status: <span id="status">checking...</span></p>
                <ol id="state"></ol>
            </article>
            <script>
                if (navigator.geolocation) {
                    alert(navigator.geolocation.getCurrentPosition(success, error));
                } else {
                    error('not supported');
                }
            </script>
        </section>
    </body>
</html>