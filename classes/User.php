<?php 

require_once "Database.php";

class User extends Database {
    // The logic of our app will be place here.

    /**
     *  Method to store registration details
     */
    public function store($request){
        $first_name = $request['first_name'];
        $last_name = $request['last_name'];
        $username = $request['username'];
        $password = $request['password'];
        // $request should be an array so that we can use an array that we get in register-action.php($user->store($_POST))

        # Hash the password
        $password = password_hash($password, PASSWORD_DEFAULT);
        // admin12345 --> siuy17525&&%8974_*&&

        #SQL query string
        $sql = "INSERT INTO users (`first_name`, `last_name`, `username`, `password`) VALUES ('$first_name', '$last_name', '$username', '$password')";
        // we use `` when we refer to the name of column, while we use '' when we refer to the name of values that we want to insert
        // (`` can be omitted, but '' cannot be)

        # Execute the query
        if ($this->conn->query($sql)) {
            header('location: ../views');  // go to index.php or login page
            exit();                        // same as die function
        } else {
            die("Error in creating the user: ". $this->conn->error);
        }
    }

    /**
     * Method to login
     */

     public function login($request) {
        $username = $request['username'];
        $password =$request['password'];

        #query string
        $sql = "SELECT * FROM users WHERE username = '$username'";
        // check the username first in terms of security

        $result = $this->conn->query($sql);

        #Check if the username exists
        if ($result->num_rows == 1) {
            # Check against the database if the password is correct
            $user = $result->fetch_assoc();
            # $user = ['id' => 1, 'username' => 'john', 'password' => '$123342q23...']

            if (password_verify($password, $user['password'])) {
                // password_verify(password that user typed, password that is already in the database)
                # Create session variables if the password matched
                session_start();
                $_SESSION['id']         = $user['id'];
                $_SESSION['username']   = $user['username'];
                $_SESSION['full_name']  = $user['first_name']." ".$user['last_name'];
                // $_SESSION is a name of associative array.
                // its contents should be like this↓
                
                /**
                 * $_SESSION = array(
                 * 'id'        => 1,                 // ユーザーのID
                 * 'username'  => 'john_doe',        // ユーザー名
                 * 'full_name' => 'John Doe',        // フルネーム
                 * );
                 */

                header('location: ../views/dashboard.php');
                exit;
            } else {
                die('Password in incorrect.');
            } 
        } else {
            die('Username is not found.');
        }

     }

     /**
      *  Logout function
      */
      public function logout(){
        session_start();
        session_unset();
        session_destroy();

        header('location: ../views');  // redirect to the login page
        exit();
      }

      /**
       * Get all users and display to dashboard
       */
      public function getAllUsers() {
        $sql = "SELECT id, first_name, last_name, username, photo FROM users";

        if ($result = $this->conn->query($sql)) {
            return $result;
        } else {
            die("Error retrieving all users: ". $this->conn->error);
        }
      }

      /**
       * retrieved specific user to edit
       */

       public function getUser($id) {
        $sql = "SELECT * FROM users WHERE id=$id";
        if ($result = $this->conn->query($sql)) {
            return $result->fetch_assoc();
        } else {
            die("Error in retrieving the user.".$this->conn->error);
        }
       }

       /**
        *  Method use to update user details
        */
        public function update($request, $files) {
            session_start();
            $id = $_SESSION['id']; // id of the user who is currently logged-in
            $first_name = $request['first_name'];
            $last_name = $request['last_name'];
            $username = $request['username'];

            $photo = $files['photo']['name'];
            $tmp_photo = $files['photo']['tmp_name'];
            // temporary

            $sql ="UPDATE users SET  first_name='$first_name', last_name='$last_name', username='$username' WHERE id = $id";

            if ($this->conn->query($sql)) {
                $_SESSION['username'] = "$username";
                $_SESSION['full_name'] = "$first_name $last_name";

                //  if there is  an uploaded photo, save it to the db and save the file to the images folder

                if ($photo) {
                    $sql = "UPDATE users SET photo = '$photo' WHERE id = $id";
                    $destination = "../assets/images/$photo";

                    # save the image name to the database
                    if ($this->conn->query($sql)) {
                        // save the image to the images folder
                        if (move_uploaded_file($tmp_photo, $destination)) {
                            header('location: ../views/dashboard.php');
                            exit;
                        } else {
                            die('Error moving the photo.');
                        }
                    } else {
                        die('Error uploading photo: '.$this->conn->error);
                    }
                } else {
                    header('location: ../views/dashboard.php');
                    exit;
                }
            } else {
                die("Error updating the user:" .$this->conn->error);
            }
        }

        /**
         *  Method to delete user account
         */

         public function delete() {
            session_start();
            $id = $_SESSION['id'];

            $sql ="DELETE FROM users WHERE id=$id";
            if ($this->conn->query($sql)) {
                $this->logout();
            } else {
                die('Erro deleting your account: '. $this->conn->error);
            }
}

?>