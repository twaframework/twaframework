/********
 *  Requires AWS package
 *
 *
 * @param id
 * @param data
 */

function twaUploader(id , data){
    this.id = id;
    this.properties = {
        destination: "images/temp/",
        allowed_types: [],
        allowed_extensions: [],
        //allowed_types: ['image/jpeg','image/jpg','image/png','image/gif','application/msword','application/vnd.ms-powerpoint','application/pdf','application/vnd.ms-excel','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/vnd.openxmlformats-officedocument.presentationml.presentation','application/vnd.openxmlformats-officedocument.presentationml.slideshow','application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','multipart/x-zip'],
        //allowed_extensions: ['.jpg','.jpeg','.png','.gif','.pdf','.zip','.xls','.xlsx','.doc','.docx','.txt','.ppt','.pptx','.pps','.ppsx']
        onError: function(x){
            unitiques.showError(unitiques.getError(x));
        }
    };

    $.extend(this.properties, data);
    this.init();
}

twaUploader.prototype.init = function(){
    var me = this;
    if(typeof me.properties.drop_target == 'undefined'){
        return;
    }
    console.debug(me.properties);
    if(typeof me.properties.upload_button != 'undefined'){
        me.properties.upload_button.click(function(){

            me.properties.input.click();
        });
    }

    me.properties.drop_target.filedrop({
        fallback_id: me.properties.input.attr('id'),   // an identifier of a standard file input element
        url: $baseurl+'webservices.php',              // upload handler, handles each file separately
        paramname: 'file',          // POST parameter name used on serverside to reference file
        data: {
            axn: "framework/file",
            code: 'upload',
            twa_token: $authtoken,
            files: [
                {
                    "param":"file",
                    "destination": me.properties.destination
                }
            ]
        },
        error: function(err, file) {
            var name = file.name.replace(/[\. ,:-]+/g, "-");
            if(typeof me.onError == 'function'){
                me.onError({
                    errorCode: err,
                    name: name
                });
            }

        },
        allowedfiletypes: me.properties.allowed_types,
        allowedfileextensions: me.properties.allowed_extensions,
        maxfiles: 1,
        maxfilesize: 10,    // max file size in MBs
        docEnter: function() {
            if(typeof me.properties.onDocEnter == 'function'){
                me.properties.onDocEnter();
            }
        },
        docLeave: function() {
            if(typeof me.properties.onDocLeave == 'function'){
                me.properties.onDocLeave();
            }
        },
        drop: function(e) {
            if(typeof me.properties.onDrop == 'function'){
                me.properties.onDrop(e);
            }
        },
        uploadStarted: function(i, file, len){
            if(typeof me.properties.onUploadStarted == 'function'){
                me.properties.onUploadStarted(i,file,len);
            }
        },
        uploadFinished: function(i, file, response, time) {
            console.debug("Upload Return",response);
            if(response.returnCode == 0 && response.files.length > 0) {
                if(typeof me.properties.onUploadComplete == 'function'){
                    me.properties.onUploadComplete(response,file);
                }
            } else {
                me.onError(response.errorCode);
                console.debug("Upload Failed",response);
            }
        },
        progressUpdated: function(i, file, progress) {
            // this function is used for large files and updates intermittently
            // progress is the integer value of file being uploaded percentage to completion
            if(typeof me.properties.updateProgress == 'function'){
                me.properties.updateProgress(progress, file);
            }
        },
        beforeSend: function(file, i, done) {
            if(typeof me.properties.min_width != 'undefined') {
                var fr = new FileReader;

                fr.onload = function () { // file is loaded
                    var img = new Image;

                    img.onload = function () {
                        if (img.width >= me.properties.min_width && img.height >= me.properties.min_height) {
                            done();
                        } else {
                            if (typeof onError == 'function') {
                                onError("ImageTooSmall");
                            }
                        }
                    };
                    img.src = fr.result; // is the data URL because called with readAsDataURL
                };

                fr.readAsDataURL(file);
            } else {
                done();
            }
        }
    });
};

twaUploader.prototype.upload_to_cdn = function(data,onSuccess, onError){
    $framework.request({
        axn:"aws/services",
        code: "cdn",
        bucket: data.bucket,
        key: data.key,
        file: data.file
    },function(r){
        if(typeof onSuccess == 'function'){
            onSuccess(r);
        }
    },function(x){
        if(typeof onError == 'function'){
            onError(x);
        }
    });
};

twaUploader.prototype.onError = function(x){
    var me = this;
    console.debug("Upload Error",x);
    if(typeof me.properties.onError == 'function'){
        me.properties.onError(x);
    }
};

function ImageUploader(id, data){
    this.id = id;
    this.properties = {
        destination: "images/temp/",
        allowed_types: ['image/jpeg','image/jpg','image/png','image/gif'],
        allowed_extensions: ['.jpg','.jpeg','.png','.gif'],
        min_width: 0,
        min_height: 0
    };
    $.extend(this.properties, data);
    this.init();
}

ImageUploader.inheritsFrom(twaUploader);

ImageUploader.prototype.write_image = function(data,onSuccess, onError){
    var d = {
        "axn":"framework/image",
        "code":"write",
        "format":"jpeg"
    };
    $.extend(d,data);
    $framework.request(d,function(a){
        if(typeof onSuccess == 'function'){
            onSuccess(a);
        }
    },function(x){
        if(typeof onError == 'function'){
            onError(x);
        }
    });
};

ImageUploader.prototype.resize = function(data,onSuccess, onError){
    var d = {
        "axn":"framework/image",
        "code":"resize",
        "format":"jpeg"
    };
    $.extend(d,data);
    $framework.request(d,function(a){
        if(typeof onSuccess == 'function'){
            onSuccess(a);
        }
    },function(x){
        if(typeof onError == 'function'){
            onError(x);
        }
    });
};


ImageUploader.prototype.thumbnail = function(data,onSuccess, onError){
    var d = {
        "axn":"framework/image",
        "code":"thumbnail",
        "format":"jpeg"
    };
    $.extend(d,data);
    $framework.request(d,function(a){
        if(typeof onSuccess == 'function'){
            onSuccess(a);
        }
    },function(x){
        if(typeof onError == 'function'){
            onError(x);
        }
    });
};
