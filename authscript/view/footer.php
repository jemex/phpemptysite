</div>
      </div>

    </div>  
  </section>
<!---- Modal ----->
<div class="edit_time">
<div class="modal fade bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<div class="betdata row"></div>
</div>
</div>
</div>
</div>

<div class="">
<div class="modal fade submitPayment" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
<div class="modal-dialog modal-lg" role="document">
<div class="modal-content">
    <div class="col-sm-12">
    <form action="<?= $GLOBALS['ep_dynamic_url'] . 'adminmain/wallet_withdraw' ?>" method="post">
        <input type="hidden" class="withdrwal_amount form-control" name="amount"/>
        <input type="hidden" class="user_id form-control" name="user_id"/>
        <input type="hidden" name="wallet_id" class="wallet_id"/>
        <div class="form-group">
            <label class="control-label">Transaction Id</label>
            <input type="text" name="transfer_id" class="form-control"/>
        </div>
        <input type="submit" value="submit"/>
    </form>
    </div>
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<div class="betdata row"></div>
</div>
</div>
</div>
</div>
  

<!-- jquery min -->
<script type="text/javascript" src="<?php echo $GLOBALS['ep_dynamic_url']; ?>view/js/vendor/3.1.1.jquery.min.js"></script>
<!-- bootstrap -->
<script type="text/javascript" src="<?php echo $GLOBALS['ep_dynamic_url']; ?>view/js/vendor/bootstrap.min.js"></script>
<!-- animated js -->
<script type="text/javascript" src="<?php echo $GLOBALS['ep_dynamic_url']; ?>view/js/animate/wow.min.js"></script>
<!-- bootstrap -->
<script type="text/javascript" src="<?php echo $GLOBALS['ep_dynamic_url']; ?>view/js/custom.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>

<script type="text/javascript">
function validatePassword() {
        var validator = $("#formCheckPassword").validate({
            rules: {
                password1: {
                    "required": true,
                     minlength:8,
                },
                password2: {
                    equalTo: "#password1",
                }
            },
            messages: {
                password1:{
                    required: "Enter Password",
                    minlength: "Enter at least 8 characters",
                } ,
                password2: "Enter Confirm Password Same as Password",
            }
        });
        if (validator.form()) {
           
        }
    }

</script>
</body>
</html>