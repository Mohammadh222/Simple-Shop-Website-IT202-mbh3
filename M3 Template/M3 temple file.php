<!DOCTYPE html>
<html lang="en-US">

<head>
    <title>Problem 1</title>
    <meta charset="utf-8" />
    <script>
        window.addEventListener("load", () => {
            console.log("loaded via javascript");
            //TODO: add any extra onload processing you may need here
        });

        function getCurrentSelection() {
            setTimeout(() => {
                //TODO: add code for processing the current selection 

                // likely you'll want to call updateCurrentPage towards the end
            }, 100);
        }

        // Function to update the page content and title
        function updatePageContent(linkText) {
            updateCurrentPage(linkText);
            document.querySelector('h1').textContent = linkText;
            document.title = linkText;
        }
    </script>
    <style>
        /* Horizontal Navigation Bar */
        nav ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #333; /* Background for navigation */
        }

        /* Navigation Links */
        nav ul li {
            float: left;
        }

        nav ul li a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 16px;
            text-decoration: none;
        }

        /* Mouseover color change */
        nav ul li a:hover {
            background-color: #111;
        }

        /* Checkmarks instead of bullet points */
        ul {
            list-style: none; /* Remove default list styling */
        }
        ul li::before {
            content: '✓'; /* Add checkmark before list item */
            padding-right: 8px;
        }

        /* Uppercase the first character */
        h1::first-letter, a::first-letter {
            text-transform: uppercase;
        }

        /* Additional Styling */
        h1 {
            color: #007bff; /* Your chosen style */
            font-size: 2em;
        }

        /* Ensure nav styles do not affect other lists */
        body > ul li::before {
            content: ''; /* Override checkmarks for main content list */
        }
    </style>
    <!-- make the necessary edits above this line -->


    <!-- Do not edit anything below this line, doing so will lose points-->
    <script src="https://it202-spring-22.herokuapp.com/M3/problem1.js">
        //Don't edit anything in this tag and do not delete it
    </script>
</head>
<!-- Do not edit anything below this line, doing so will lose points-->

<body onload="check();updateCurrentPage('start');">
    <header>
        <h2>Problem 1</h2>
    </header>
    <nav>
        <ul>
            <li><a href="#login" onclick="updatePageContent('login')">login</a></li>
            <li><a href="#register" onclick="updatePageContent('register')">register</a></li>
            <li><a href="#profile" onclick="updatePageContent('profile')">profile</a></li>
            <li><a href="#logout" onclick="updatePageContent('logout')">logout</a></li>
        </ul>
    </nav>
    <h1></h1>
    <h3>Challenges</h3>
    <ul>
        <li>Edit the given <code>&lt;style&gt;</code> tag to customize the appearance of this page
            <!-- The rest of your content here -->
        </li>
    </ul>
    <footer></footer>
</body>

</html>
