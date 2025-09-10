<?php
include '../include/connection.php';
$output='';
if(isset($_POST['query'])) {
    $search=$_POST['query'];
    $stmt=$conn->prepare("SELECT * FROM products WHERE name LIKE CONCAT('%',?,'%')");
    $stmt->bind_param("s", $search); 
} else {
    $stmt=$conn->prepare("SELECT * FROM products");
}
$stmt->execute();
$result=$stmt->get_result();
if($result->num_rows > 0){
    $output = "<thead  id='theadOfmanageProductTable' >
                    <tr>
                        <th class='tobLeftRedius-ProductTable'>S.N.</th>
                        <th>Picture</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th class='tobRightRedius-ProductTable'>Actions</th>
                    </tr>
                </thead>
                <tbody  id='tbodyOfmanageProductTable'>";
    $count = 1;
    while($row = $result->fetch_assoc()){
        $output .= "<tr>
                        <td>".$count."</td>
                        <td><img src='data:image/jpeg;base64,".base64_encode($row['picture'])."' alt='".$row['name']."'></td>
                        <td>".$row['name']."</td>
                        <td>".$row['description']."</td>
                        <td>".$row['price']." SAR</td>
                        <td>".$row['stock']."</td>
                        <td>
                            <div class='admin-actions'>
                                <form method='post' action='' class='form-reset'>
                                    <input type='hidden' name='product_ID' value='".$row['id']."'>
                                    <input type='submit' name='deleteButton' value='Delete' class='btn-primary' onclick=\"return confirmDelete('" . $row['name'] . "');\">
                                </form>
                                <form action='editProduct-page.php' method='get' class='form-reset'>
                                    <input type='hidden' name='id' value='".$row['id']."'>
                                    <input type='submit' value='Edit' class='btn-primary'>
                                </form>
                            </div>
                        </td>
                    </tr>";
        $count++;
    }
    $output .= "</tbody>";
    echo $output;
} else {
    echo "<tr  id='tbodyOfmanageProductTable'> <td colspan='7'>No Products Found!</td></tr>";
}
?>

<script type="text/javascript">
    function confirmDelete(productName) {
        return confirm("Are you sure you want to delete " + productName + "?");
    }
</script>
