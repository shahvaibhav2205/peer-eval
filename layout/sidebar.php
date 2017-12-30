    <nav id="sidebar">
                <div class="sidebar-header">
                    <h4>Peer Evals</h4>
                </div>

                <ul class="list-unstyled components">
                   <p><?php
                        if ((isset($_SESSION['userType'])) && $_SESSION["userType"] === "student") {
                            $user_name = $user->getStudentName($_SESSION['email']);
                        } else {
                            $user_name = $user->getFacultyName($_SESSION['email']);
                        }
                  
                   echo "Hello, ".$user_name['firstname']." ".$user_name['lastname']; ?></p>

                    <p> <a class="btn btn-success btn-sm" href="manage-peerevals.php">Start Peer Eval</a></p>
                    <li>
                        <a href="templates.php">Templates</a>
                    </li>
                    <li>
                        <a href="classes.php">Classes</a>
                    </li>
                    <li>
                        <a href="peerevals.php">Peer Evals</a>
                    </li>
                    
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