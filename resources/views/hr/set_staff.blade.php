<div id="setStaff" class="modal fade">
    <button id="openSetStaff" data-toggle="modal" href="#setStaff" class="hidden"></button>
    <div class="modal-dialog">
        <div class="modal-header">
            <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
            <h4 class="modal-title">关联员工</h4>
        </div>
        <div class="modal-content">
            <form id="setStaffForm" class="form-horizontal" method="post"
                  action="{{empty($submitUrl)? $submitUrl: asset(route('hr.staff.multi_set'))}}">
                <div class="modal-body">
                    <button type="button" class="btn btn-default m-bot15" onclick="searchStaff()"><i
                                class="fa fa-user-plus"></i></button>
                    <style>
                        .alert.alert-tag {
                            padding: 10px 15px 10px 20px;
                            border-radius: 20px;
                            display: inline-block;
                        }

                        .alert.alert-tag .close {
                            font-size: 14px;
                            line-height: 20px;
                            margin-left: 10px;
                        }
                    </style>
                    <div id="staff_tag">
                        @foreach($staff as $v)
                            <div class="alert alert-success alert-tag">
                                <button type="button" class="close close-sm" data-dismiss="alert"><i
                                            class="fa fa-times"></i></button>
                                <a href="#" class="alert-link">{{$v->realname}}</a>
                                <input type="hidden" name="staff[]" value="{{$v->staff_sn}}">
                            </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="eloquent" value="{{$eloquent}}">
                    @if(isset($primary['key']))
                        <input type="hidden" name="primary[key]" value="{{$primary['key']}}">
                    @endif
                    <input type="hidden" name="primary[value]" value="{{$primary['value']}}">
                    {{csrf_field()}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">取消</button>
                    <button type="button" class="btn btn-success" onclick="submitStaffByAjax()">确认</button>
                </div>
            </form>
        </div>
    </div>
    <script>
      $(function () {
        $("#setStaff").on("click", function (event) {
          if (event.target.id === "setStaff" || event.target.getAttribute("data-dismiss") === "modal") {
            $("#setStaff,#setStaff+.modal-backdrop").fadeOut(300, function () {
              $(this).remove();
            });
          }
        });
      });

      function searchStaff() {
        var url = "/hr/staff/search";
        $.ajax({
          type: "POST",
          url: url,
          dataType: 'text',
          success: function (msg) {
            $("body").append(msg);
            $("#openSearchStaffResult").click();
          },
          error: function (err) {
            document.write(err.responseText);
          }
        });
      }

      function searchStaffClick(event) {
        var data = event.data;
        var html = '<div class="alert alert-success alert-tag">' +
          '<button type="button" class="close close-sm" data-dismiss="alert"><i class="fa fa-times"></i></button>' +
          '<a href="#" class="alert-link">' + data.realname + '</a>' +
          '<input type="hidden" name="staff[]" value="' + data.staff_sn + '">' +
          '</div>';
        $("#staff_tag").append(html);
        $("#searchStaffResult .close").click();
      }

      function submitStaffByAjax() {
        $("#waiting").fadeIn(200);
        var url = $("#setStaffForm").attr("action");
        var data = $("#setStaffForm").serializeArray();
        var type = $("#setStaffForm").attr('method');
        $.ajax({
          type: type,
          url: url,
          data: data,
          dataType: 'json',
          success: function (msg) {
            if (msg['status'] === 1) {
              $("#setStaff .close").click();
              if (typeof setStaffCallback == 'function') {
                setStaffCallback();
              }
              $("#waiting").fadeOut(300);
            }
          },
          error: function (err) {
            document.write(err.responseText);
          }
        });
      }

    </script>
</div>
