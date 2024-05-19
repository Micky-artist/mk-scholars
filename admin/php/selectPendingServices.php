<?php
$selectServices = mysqli_query($conn, "SELECT * FROM services WHERE servicestatus=0 ORDER BY uploadDate DESC");
if ($selectServices->num_rows > 0) {
    while ($getService = mysqli_fetch_assoc($selectServices)) {
        ?>
        <div class="d-flex flex-row comment-row mt-0">
            <div class="p-2">
                <img width="100" src="./uploads/services/<?php echo $getService['image1'] ?>" />
            </div>
            <div class="comment-text w-100">
                <h6 class="font-medium">
                    <?php echo $getService['servicename'] ?>
                </h6>
                <!-- <span class="mb-3 d-block">Lorem Ipsum is simply dummy text of the printing and
                      type setting industry.
                    </span> -->
                <div class="comment-footer">
                    <span class="text-muted float-end">
                        <?php echo $getService['uploadDate'].' '.$getService['uploadTime'] ?>
                    </span>
                    <a
                        href="edit-service?edit=true&i=<?php echo $getService['serviceId'] ?>&n=<?php echo $getService['servicename'] ?>">
                        <button type="button" class="btn btn-cyan btn-sm text-white">
                            Edit
                        </button>
                    </a>
                    <a
                        href="https://www.icyezainteriors.com/services-prev?d=<?php echo $getService['serviceId']?>&n=<?php echo $getService['servicename']?>">
                        <button type="button" class="btn btn-warning btn-sm text-white">
                            View
                        </button>
                    </a>
                    <?php
                    if ($getService['servicestatus'] == 0) {
                        ?>
                        <a href="./php/actions?a=publishService&i=<?php echo $getService['serviceId']?>&n=<?php echo $getService['servicename']?>">
                        <button name="publishService" class="btn btn-success btn-sm text-white">
                            Publish
                        </button>
                        </a>
                        <?php
                    } else {
                        ?>
                        <a href="./php/actions?a=unPublishService&i=<?php echo $getService['serviceId']?>&n=<?php echo $getService['servicename']?>">
                        <button name="unPublishService" class="btn btn-success btn-sm text-white">
                            Un Publish
                        </button>
                        </a>
                        <?php
                    }
                    ?>
                    <a href="./php/actions?a=deleteService&i=<?php echo $getService['serviceId']?>&n=<?php echo $getService['servicename']?>">
                    <button name="deleteService" class="btn btn-danger btn-sm text-white">
                        Delete
                    </button>
                    </a>
                </div>
            </div>
        </div>
        <!-- <li>
            <a href="services?d=<?php echo $getService['serviceId'] ?>&n=<?php echo $getService['servicename'] ?>"><?php echo $getService['servicename'] ?></a>
        </li> -->
        <?php
    }
} else {
    ?>
    <div class="d-flex flex-row comment-row mt-0">
        <div class="comment-text w-100">
            <h6 class="font-medium">
                No pending service approvals
            </h6>
        </div>
    </div>
    <?php
}
?>