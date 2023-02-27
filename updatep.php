<?php
// Include config file
require_once "config.php";
 
// Define variables and initialize with empty values
$name = $year = $descripcion = $url = "";
$name_err = $year_err = $descripcion_err = $url_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name.";
    } else{
        $name = $input_name;
    }
    
    // Validate year 
    $input_year = trim($_POST["year"]);
    if(empty($input_year)){
        $year_err = "Please enter an year.";     
    } else{
        $year = $input_year;
    }
    
    // Validate descripcion
    $input_descripcion = trim($_POST["descripcion"]);
    if(empty($input_descripcion)){
        $descripcion_err = "Please enter the descripcion.";     
    } 
    else{
        $descripcion = $input_descripcion;
    }
    // Validate url
    $input_url = trim($_POST["url"]);
    if(empty($input_url)){
        $url_err = "Please enter the url.";     
    } 
    else{
        $url = $input_url;
    }    
    // Check input errors before inserting in database
    if(empty($name_err) && empty($year_err) && empty($descripcion_err)){
        // Prepare an update statement
        $sql = "UPDATE peliculas SET name=?, year=?, descripcion=? WHERE id=?";
         
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "sssi", $param_name, $param_year, $param_descripcion, $param_id);
            
            // Set parameters
            $param_name = $name;
            $param_year = $year;
            $param_descripcion = $descripcion;
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                // Records updated successfully. Redirect to landing page
                header("location: indexp.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
         
        // Close statement
        mysqli_stmt_close($stmt);
    }
    
    // Close connection
    mysqli_close($link);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM peliculas WHERE id = ?";
        if($stmt = mysqli_prepare($link, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $name = $row["name"];
                    $year = $row["year"];
                    $descripcion = $row["descripcion"];
                    $url = $row["url"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($link);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Record</h2>
                    <p>Please edit the values and submit to update the movie record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Year</label>
                            <textarea name="year" class="form-control <?php echo (!empty($year_err)) ? 'is-invalid' : ''; ?>"><?php echo $year; ?></textarea>
                            <span class="invalid-feedback"><?php echo $year_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Descripcion</label>
                            <input type="text" name="descripcion" class="form-control <?php echo (!empty($descripcion_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $descripcion; ?>">
                            <span class="invalid-feedback"><?php echo $descripcion_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>url</label>
                            <input type="text" name="url" class="form-control <?php echo (!empty($url_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $url; ?>">
                            <span class="invalid-feedback"><?php echo $url_err;?></span>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="indexp.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>
