<nav id="sidebar">
    <div class="sidebar-header">
        <h4>Peer Evals</h4>
    </div>

    <ul class="list-unstyled components">
        <p>
            <?php
        if ((isset($_SESSION['userType'])) && $_SESSION["userType"] === "student") {
            $user_name = $user->getStudentName($_SESSION['email']);
        } else {
            $user_name = $user->getFacultyName($_SESSION['email']);
        }
        echo "Hello, ".$user_name['firstname']." ".$user_name['lastname']; ?>
        </p>
        <?php
        if ((isset($_SESSION['userType'])) && $_SESSION["userType"] === "student") { // if student
        ?>
        <li>
            <a href="pending-evals.php">Pending</a>
        </li>
        <li>
            <a href="completed-evals.php">Completed Evaluations</a>
        </li>
        <?php
        } else { // if user is a faculty
        ?>
        <li>
            <a href="templates.php">Templates</a>
        </li>
        <li>
            <a href="classes.php">Classes</a>
        </li>

        <?php
        }
        ?>
    </ul>

    <hr>
    <ul class="list-unstyled components">
        <li>
            <a href="#">Settings</a>
        </li>
        <li>
            <a href="logout.php">Logout</a>
        </li>
    </ul>
</nav>