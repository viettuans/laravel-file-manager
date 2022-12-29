<div class="modal fade" id="modal-media" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width:80%;" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thư viện hình ảnh</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true"><i class="fal fa-times"></i></span>
                </button>
            </div>
            <div class="modal-body">
                <form method="post" data-toggle="tooltip" title="Click vào khoảng trắng để tải hình ảnh lên"
                    action="{{ route('media.upload') }}" data-list-url="{{ route('media.list') }}"
                    data-delete-url="{{ route('media.delete') }}" class="dropzone needsclick" id="dropzone"
                    style="min-height: 4rem;" enctype="multipart/form-data">
                    @csrf
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                <button type="submit" class="btn btn-primary dz-btn-select-image">Chọn</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" media="screen, print" href="{{ asset('vendor/laravel-filemanager/css/dropzone.css') }}">
<style>
    .dropzone .dz-preview .dz-image img {
        width: 100%
    }
</style>
<script src="{{ asset('vendor/laravel-filemanager/js/dropzone.js') }}"></script>
<script>
    const modalMedia = $('#modal-media');
    const formMedia = $('#dropzone');
    const previewItem = '.dz-preview';
    const btnSelectImage = '.dz-btn-select-image';
    const boxDzGroup = '.box-dz-group';
    let dzGroupEle = $(boxDzGroup);

    $(document).on('click', '.btn-select-image', function() {
        dzGroupEle = $(this).closest(boxDzGroup);
        modalMedia.modal();
    });

    $(document).on('click', previewItem, function() {
        $(previewItem).removeClass('dz-preview-active');
        $(this).addClass('dz-preview-active');
    });

    function handleSelectImage(itemEle) {
        modalMedia.modal('hide');
        let path = itemEle.data('path');
        let fullPath = itemEle.data('full-path');
        dzGroupEle.find('.image-preview').attr('src', fullPath);
        dzGroupEle.find('.image-input').val(path);
    }

    $(document).on('dblclick', previewItem, function() {
        handleSelectImage($(this));
    });

    $(document).on('click', btnSelectImage, function() {
        const previewSelected = $('.dz-preview-active');
        if (previewSelected.length) {
            handleSelectImage(previewSelected);
        } else {
            toastr.warning('Xin vui lòng chọn hình ảnh');
        }
    });


    Dropzone.options.dropzone = { // camelized version of the `id`
        paramName: "image", // The name that will be used to transfer the file
        maxFilesize: 5, // MB
        uploadMultiple: false,
        addRemoveLinks: true,
        acceptedFiles: ".jpeg,.jpg,.png,.gif",
        resizeWidth: "500",
        clickable: true,
        init: function() {
            this.on("complete", function(file) {
                $(".dz-remove").html(
                    '<i class="icon-delete fal fa-trash-alt text-danger" data-toggle="tooltip" title=""></i>'
                );

                $(".dz-remove").attr('title', 'Xóa hình ảnh');
                $(".dz-remove").attr('data-toggle', 'tooltip');
            });

            var myDropzone = this;
            // get list image
            $.ajax({
                url: formMedia.data('list-url'),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        $.each(data.data, function(key, value) {
                            var file = {
                                name: value.name,
                                size: value.size
                            };
                            myDropzone.options.addedfile.call(myDropzone, file);
                            myDropzone.options.thumbnail.call(myDropzone, file, value
                                .full_path);
                            myDropzone.emit("complete", file);
                            $(file.previewTemplate).attr('data-path', value.path);
                            $(file.previewTemplate).attr('data-full-path', value.full_path);
                        });
                    } else {
                        toastr.error(data.message);
                    }
                },
                error: function(request, status, error) {
                    toastr.error("Lỗi hệ thống");
                }
            });
        },
        success: function(file, response) {
            if (response.status) {
                $(file.previewTemplate).attr('data-path', response.data.path);
                $(file.previewTemplate).attr('data-full-path', response.data.full_path);
            } else {
                toastr.error(response.message);
            }
        },
        error: function(file, response) {
            return false;
        },
        removedfile: function(file) {
            if (this.options.dictRemoveFile) {
                return Dropzone.confirm("Bạn có chắc muốn xóa hình ảnh " + file.name,
                    function() {
                        if (file.previewElement.id != "") {
                            var name = file.previewElement.id;
                        } else {
                            var name = file.name;
                        }
                        $.ajax({
                            type: 'POST',
                            url: formMedia.data('delete-url'),
                            data: {
                                filename: name
                            },
                            success: function(data) {
                                if (data.status) {
                                    toastr.success("Đã xóa hình ảnh thành công!");
                                } else {
                                    toastr.error(data.message);
                                }
                            },
                            error: function(e) {
                                console.log(e);
                                toastr.error("Lỗi hệ thống");
                            }
                        });
                        var fileRef;
                        return (fileRef = file.previewElement) != null ?
                            fileRef.parentNode.removeChild(file.previewElement) : void 0;
                    });
            }
        },
        dictDefaultMessage: "Click hoặc kéo thả hình ảnh vào đây",
        dictFallbackMessage: "Trình duyệt của bạn không hỗ trợ chức năng kéo thả",
        dictInvalidFileType: "Kiểu file không hợp lệ, chung tôi chỉ hỗ trợ dạng file .jpeg,.jpg,.png,.gif",
    };
</script>
