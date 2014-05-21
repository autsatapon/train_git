/** @jsx React.DOM */
var ReactBulkUpload = null,

BulkUpload = React.createClass({
	getInitialState: function() {
		return {
			items: [],
			dropzone: null,
			queuedFiles: false,
			uploadStatus: null, // uploading, failed, completed
			uploadProgress: 0
		};
	},

	findItem: function(id) {
		return _.find( this.state.items, function(item) {
			return item.id === id;
		});
	},

	isNotSafe: function() {
		return this.props.uploadStatus === 'uploading';
	},

	addFile: function(item) {
		var items = this.state.items;
		items.push(item);

		this.setState({
			items: items,
			queuedFiles: this.state.dropzone.getQueuedFiles()
		});
	},
	removeFile: function(id) {
		var items = this.state.items,
			index = _.indexOf(items, this.findItem(id)),
			removedItem = items.splice(index,1)[0];

		this.state.dropzone.removeFile(removedItem.file);
		this.setState({
			items: items,
			queuedFiles: this.state.dropzone.getQueuedFiles()
		});
	},

	startUpload: function(e) {
		e.preventDefault();
		if(this.state.isNotSafe)
			return false;
		
		this.setState({
			uploadStatus: 'uploading'
		});
		this.state.dropzone.processQueue();
	},
	uploadDone: function() {
		var _react = this;
		this.setState({
			queuedFiles: this.state.dropzone.getQueuedFiles(),
			uploadStatus: 'completed'
		});
		setTimeout(function(){
			_react.setState({
				uploadStatus: ''
			});
		}, 2000);
	},

	render: function(){
		return	<div class="media-row">
					<div class="media-console">
						<UploadBar ref="uploadBar" upAct={this} uploadStatus={this.state.uploadStatus} uploadProgress={this.state.uploadProgress} queuedFiles={this.state.queuedFiles}/>
					</div>
					<UploadDropzone ref="uploadDropzone" upAct={this} items={this.state.items} />
					<div className="hide" ref="hiddenDropResult" />
				</div>;
	},

	componentDidMount: function(node) {
		var _react = this,
			_refs = _react.refs,
			fileNameFormat = /^((360|img|image)\-)?(\d+)(\-\d+)?/i;

		dropzone = $(_refs.uploadDropzone.getDOMNode()).dropzone({
			maxFilesize: 3, // 3 MB
			acceptedFiles: 'image/jpeg,image/pjpeg,image/png,image/gif',
			url: '/products/set-content/bulk-upload',
			autoProcessQueue: false,
			parallelUploads: 100,
			paramName: 'media-file',
			previewsContainer: _refs.hiddenDropResult.getDOMNode(),
			clickable: true,
			accept: function(file, done) {
				if(! fileNameFormat.test(file.name))
					done('Incorrect format');
				else
					done();
			},
			init: function() {
				var processCount = 0;

				this.on('selectedfiles',function(files){
					var i, len = files.length,
						_dropzone = this;

					for(i=0; i<len; i++) {
						(function(newFile) {
							var name = newFile.name,
								nameParts = name.match(fileNameFormat),
								fileReader = new FileReader(),
								data = {
									id: name,
									src: null,
									mode: nameParts && /^(360)/.test(nameParts[2]) ? '360' : 'image',
									fid: nameParts && nameParts[3] ? nameParts[3] : null
								};
							newFile['data'] = data;

							if(nameParts && ! _react.findItem(name) ) {
								fileReader.onload = function(e) {
									_react.addFile({
										id: name,
										src: e.target.result,
										mode: data.mode,
										fid: data.fid,
										file: newFile,
										uploaded: false
									});
								}
								fileReader.readAsDataURL(newFile);
							} else {
								_dropzone.removeFile(newFile);
							}
						})(files[i]);
					}
				}).on('totaluploadprogress',function(progress){
					_react.setState({ uploadProgress:progress });
				}).on('sending',function(file){
					processCount++;
				}).on('complete',function(file){
					--processCount===0 && _react.uploadDone();
				}).on('success', function(file, data){
					if(data.success) {
						var item = _react.findItem(data.id);
						if(item) {
							item.uploaded = true;
							item.src = data.thumb;
							_react.setState({
								items: _react.state.items
							})
						}
					} else {
						alert('Failed to upload '+file.name);
					}
				}).on('error', function(file, data){
					if(! fileNameFormat.test(file.name))
						alert('Invalid file name. Please see the reference');
					else if(data && ! data.success && data.error)
						alert(data.error);
					else
						alert('Failed to upload '+file.name+'\n - Max file size is 3 MB.\n - Accept only jpg, png, gif');
				});

				_react.setState({
					dropzone: this
				});
			}
		});
	}
}),

UploadBar = React.createClass({
	render: function(){
		return	<div class="media-box clearfix">
					<i className="icon icon-hand-down"></i>{' '}Drop file below to upload multiple image{' '}
					{ this.props.uploadStatus === 'uploading'
						? <span className="label label-warning">Uploaded { Math.floor(this.props.uploadProgress) }%</span>
						: ( this.props.uploadStatus === 'completed'
							? <span className="label label-success">Uploaded { Math.floor(this.props.uploadProgress) }%</span>
							: null
						)
					}
					{
						this.props.uploadStatus !== 'uploading' && this.props.queuedFiles && this.props.queuedFiles.length>0
							? <a className="btn btn-primary pull-right" onClick={this.props.upAct.startUpload}>Start upload {this.props.queuedFiles.length>100 ? 100 : this.props.queuedFiles.length} file{this.props.queuedFiles.length>1 ? 's' : ''}</a>
							: null
					}
				</div>;
	}
});

UploadDropzone = React.createClass({
	handleRemove: function(id) {
		this.props.upAct.removeFile(id);
	},

	render: function(){
		var _react = this,
		renderContent = function(item) {
			return	<div className={'content content-'+item.mode} key={item.name}>
						<div className="content-tag">{item.mode}</div>
						<img className="thumb" src={item.src}/>
						{ item.uploaded
							? <div className="content-status"><i className="icon icon-white icon-ok"/></div>
							: <div className="content-menu">
								<a href="javascript:void(0);" onClick={_react.handleRemove.bind(this, item.id)}><i className="icon icon-remove"></i></a>
							</div>
						}
					</div>
		};
		return	<div className="media-list clearfix">
				{ this.props.items.map(renderContent) }
				</div>
	}
});

$(function(){
	$(window).on('beforeunload',function(){
		if(ReactBulkUpload.isNotSafe())
			return 'You have unsaved modification. Are you sure to leave witout saving?';
		return null;
	});

	ReactBulkUpload = React.renderComponent(<BulkUpload/>, document.getElementById('bulk-upload-component'));
})