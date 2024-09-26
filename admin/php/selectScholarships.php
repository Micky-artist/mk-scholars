<?php
$selectScholarships = mysqli_query($conn, "SELECT * FROM scholarships ORDER BY scholarshipUpdateDate DESC");
if ($selectScholarships->num_rows > 0) {
    while ($getScholarships = mysqli_fetch_assoc($selectScholarships)) {
        ?>
        <div class="d-flex flex-row comment-row mt-0">
            <div class="p-2">
                <img width="100" src="./uploads/services/<?php echo $getScholarships['scholarshipImage'] ?>" />
            </div>
            <div class="comment-text w-100">
                <h6 class="font-medium">
                    <?php echo $getScholarships['scholarshipTitle'] ?>
                </h6>
                <!-- <span class="mb-3 d-block">Lorem Ipsum is simply dummy text of the printing and
                      type setting industry.
                    </span> -->
                <div class="comment-footer">
                    <span class="text-muted float-end">
                        <?php echo $getScholarships['scholarshipUpdateDate'] ?>
                    </span>
                    <a target="_blank"
                        href="edit-service?edit=true&i=<?php echo $getScholarships['scholarshipId'] ?>&n=<?php echo $getScholarships['scholarshipTitle'] ?>">
                        <button type="button" class="btn btn-cyan btn-sm text-white">
                            Edit
                        </button>
                    </a>
                    <a target="_blank"
                        href="https://www.icyezainteriors.com/services-prev?d=<?php echo $getScholarships['scholarshipId']?>&n=<?php echo $getScholarships['scholarshipTitle']?>">
                        <button type="button" class="btn btn-warning btn-sm text-white">
                            View
                        </button>
                    </a>
                    <?php
                    if ($getScholarships['servicestatus'] == 0) {
                        ?>
                        <a href="./php/actions?a=publishService&i=<?php echo $getScholarships['scholarshipId']?>&n=<?php echo $getScholarships['scholarshipTitle']?>">
                        <button name="publishService" class="btn btn-success btn-sm text-white">
                            Publish
                        </button>
                        </a>
                        <?php
                    } else {
                        ?>
                        <a href="./php/actions?a=unPublishService&i=<?php echo $getScholarships['scholarshipId']?>&n=<?php echo $getScholarships['scholarshipTitle']?>">
                        <button name="unPublishService" class="btn btn-success btn-sm text-white">
                            Un Publish
                        </button>
                        </a>
                        <?php
                    }
                    ?>
                    <a href="./php/actions?a=deleteService&i=<?php echo $getScholarships['scholarshipId']?>&n=<?php echo $getScholarships['scholarshipTitle']?>">
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
}
?>