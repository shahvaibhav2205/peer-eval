    <nav id="sidebar">
                <div class="sidebar-header">
                    <h4>Peer Evals</h4>
                </div>

                <ul class="list-unstyled components">
                   <p><?php 
                   $user_name = $user->get_user_name($_SESSION['email']);
                   echo "Hello, ".$user_name['first_name']." ".$user_name['last_name']; ?></p>
                    <li>
                        <a href="templates.php">Templates</a>
                    </li>
                    <li>
                        <a href="classes.php">Classes</a>
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