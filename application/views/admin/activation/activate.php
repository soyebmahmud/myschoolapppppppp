<div class="content-wrapper">
    <section class="content-header">
        <h1><?php echo html_escape($title); ?></h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">Application Activation</h3>
                    </div>
                    <form action="<?php echo site_url('admin/activation/process'); ?>" method="post" autocomplete="off">
                        <div class="box-body">
                            <?php echo $this->customlib->getCSRF(); ?>
                            <?php if (!empty($error)) { ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php } ?>
                            <div class="form-group">
                                <label for="activation_code">Activation Code</label>
                                <input type="password" class="form-control" id="activation_code" name="activation_code" placeholder="SCHOOL-XXXX-XXXX-XXXX" required>
                            </div>
                        </div>
                        <div class="box-footer">
                            <button type="submit" class="btn btn-primary">Activate</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>
