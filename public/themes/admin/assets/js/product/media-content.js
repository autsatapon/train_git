/** @jsx React.DOM */
var MediaConsole = React.createClass({
	getInitialState: function(){
		return {
			mode: '',
			dirty: false,
			uploadingFile: false,
			uploadProgress: 0,
			savingChange: false
		}
	},

	setMode: function(mode){
		if(mode && this.state.mode===mode)
			this.setState({mode:''});
		else
			this.setState({mode:mode});
	},

	showImageConsole: function(e){
		e.preventDefault();
		this.setMode('image');
	},

	showVideoConsole: function(e){
		e.preventDefault();
		this.setMode('video');
	},

	show360Console: function(e){
		e.preventDefault();
		this.setMode('360');
	},

	dirt: function() {
		this.setState({
			dirty: true
		});
	},
	isDirty: function() {
		return this.state.dirty;
	},

	startUpload: function() {
		this.setState({
			uploadingFile: true
		});
	},
	uploadProgress: function(progress) {
		this.setState({
			uploadProgress: progress
		});
	},
	endUpload: function() {
		this.setState({
			uploadingFile: false,
			uploadProgress: 0
		});
	},

	saveChange: function(e){
		e.preventDefault();
		this.setState({
			mode:'',
			savingChange:true
		});
		var _react = this,
			sortNumber = 0,
			list = _react.props.mediaList.children(),
			contentOrder = [];
		list.each(function(){
			contentOrder[sortNumber++] = $(this).data('id');
		});

		$.post("/products/set-content/arrange/"+_react.props.model+"/"+_react.props.id,{
			sortOrder: contentOrder
		},function(result){
			_react.setState({
				savingChange: false,
				dirty: false
			});
		},'json');
	},

	render: function(){
		return 	<div className="media-box">
					<MediaInput mode={this.state.mode} model={this.props.model} id={this.props.id} mediaConsole={this} />
					<a className={'btn btn-primary '+(this.state.mode==='image' ? 'active' : '')} onClick={this.showImageConsole}><i className="icon icon-plus"></i>{' '}<i className="icon icon-camera"></i> Add Image</a>
					{' '}
					<a className={'btn btn-danger '+(this.state.mode==='video' ? 'active' : '')} onClick={this.showVideoConsole}><i className="icon icon-plus"></i>{' '}<i className="icon icon-facetime-video"></i> Add Youtube Link</a>
					{' '}
					<a className={'btn btn-success '+(this.state.mode==='360' ? 'active' : '')} onClick={this.show360Console}><i className="icon icon-plus"></i>{' '}<i className="icon icon-globe"></i> Add 360 picture</a>

					{
						this.state.savingChange || this.state.uploadingFile ? 
							<span className="label label-warning pull-right">
								<img src="/imgs/ajax-loading-circle-orange.gif"/>{' '}{this.state.savingChange ? 'Saving...' : 'Uploaded '+parseInt(this.state.uploadProgress)+'%'}
							</span> : (
							this.state.dirty
								? <a className="btn btn-info pull-right" onClick={this.saveChange}>Apply Change</a>
								: ''
						)
					}
				</div>
	}
});

var MediaInput = React.createClass({
	propTypes: {
		mode: React.PropTypes.oneOf(['','image','video','360']),
		model: React.PropTypes.oneOf(['product','productStyleOption','variant','variantStyleOption'])
	},

	getInitialState: function(){
		return {
			uploadingFile: false,
			uploadProgress: 0,
			fetchingYoutube: false,
			isValidYoutube: false,
			youtubeId: null,
			youtubeTitle: null,
			youtubeThumb: null
		}
	},

	init: function(domNode){
		var _react = this,
			mode = this.props.mode,
			processCount = 0;
		if(mode==='image' || mode==='360')
		{
			$(this.refs.mediaDropZone.getDOMNode()).dropzone({
				maxFilesize: 3, // 3 MB
				acceptedFiles: 'image/jpeg,image/pjpeg,image/png,image/gif',
				url: '/products/set-content/up/'+mode+'/'+this.props.model+'/'+this.props.id,
				paramName: 'uploading-image',
				previewsContainer: this.refs.dropResult.getDOMNode(),
				clickable: true,
				init: function() {
					var mediaConsole = _react.props.mediaConsole,
						template = _.template($('#media-content-template').html());

					this.on('totaluploadprogress',function(progress){
						_react.setState({ uploadProgress:progress });
						mediaConsole.uploadProgress(progress);
					}).on('sending',function(file){
						_react.setState({ uploadingFile:true });
						processCount++;
						mediaConsole.startUpload();
					}).on('complete',function(file){
						processCount--;
						if(processCount===0) {
							mediaConsole.endUpload();
							_react.setState({
								uploadProgress:0,
								uploadingFile:false
							});
						}
					}).on('success', function(file, data){
						if(data.success) {
							mediaConsole.props.mediaList.append( template(data) );
						} else {
							alert('Failed to upload '+file.name);
						}
					}).on('error', function(file){
						alert('Failed to upload '+file.name+'\n - File size limit 3 MB.\n - Accept only jpg or png');
					});
				}
			});
		}
	},

	componentDidMount: function(mountNode){
		this.init(mountNode)
	},
	componentDidUpdate: function(prevProps, prevState, domNode){
		if(prevProps.mode!==this.props.mode)
			this.init(domNode);
	},

	handleCloseBtn: function(e){
		this.props.mediaConsole.setMode('');
	},

	youtubeApi: null,
	getYoutubeData: function(){
		var _react = this,
			link = _react.refs.youtubeLink.getDOMNode().value,
			youtubeId = null;

		clearTimeout(_react.youtubeApi);
		_react.setState({
			isValidYoutube: false,
			fetchingYoutube: false,
			youtubeId: null,
			youtubeTitle: null,
			youtubeThumb: null
		});

		if(/^(https?:\/\/)?(www\.)?youtube\.com.*v=[a-z0-9\-\_]+/i.test(link))
			youtubeId = link.match(/v=([a-z0-9\-\_]+)/i)[1];

		if(youtubeId) {
			_react.setState({ fetchingYoutube: true });
			setTimeout(function(){
				/** http://gdata.youtube.com/feeds/api/videos/M6XR5_ja7Ro?v=2&alt=json&prettyprint=true **/
				$.getJSON('http://gdata.youtube.com/feeds/api/videos/'+youtubeId+'?v=2&alt=json&callback=?',function(json){
					_react.setState({
						isValidYoutube: true,
						fetchingYoutube: false,
						youtubeId: youtubeId,
						youtubeTitle: json.entry.title.$t,
						youtubeThumb: json.entry.media$group.media$thumbnail[0].url,
						youtubeImage: json.entry.media$group.media$thumbnail[2].url
					});
				});
			}, 500);
		}
	},
	saveYoutube: function(){
		var _react = this,
			mediaConsole = _react.props.mediaConsole,
			template = _.template($('#media-content-template').html()),
			ss = _react.refs.youtubeSS.getDOMNode().files.length && _react.refs.youtubeSS.getDOMNode().files[0];

		mediaConsole.startUpload();
		mediaConsole.uploadProgress(Math.floor((Math.random()*70)+20));

		if(ss) {
			if(ss.size > 3*1024*1024 || (ss.type!=='image/jpeg' && ss.type!=='image/jpg' && ss.type!=='image/png')) {
				alert('Failed to upload '+ss.name+'\n - File size limit 3 MB.\n - Accept only jpg or png');
				return false;
			}
			var reader = new FileReader();
			reader.onload = function(event) {
				var content = event.target.result;
				_react.postYoutube(_react.state.youtubeId, content, false);
			}
			reader.readAsDataURL(_react.refs.youtubeSS.getDOMNode().files[0]);
		} else {
			_react.postYoutube(_react.state.youtubeId, _react.state.youtubeImage, true);
		}
		
	},
	postYoutube: function(youtubeId, image, isRemoteImage) {
		var _react = this,
		mediaConsole = _react.props.mediaConsole,
		template = _.template($('#media-content-template').html()),
		postData = {
			'youtube-id': youtubeId
		};

		if(isRemoteImage)
			postData['remote-image'] = image;
		else
			postData['upload-screenshot'] = image;

		$.post("/products/set-content/up/youtube/"+this.props.model+"/"+this.props.id,postData,function(data){
			if(data.success) {
				mediaConsole.props.mediaList.append( template(data) );
				_react.refs.youtubeLink.getDOMNode().value = '';
				_react.setState({
					isValidYoutube: false,
					fetchingYoutube: false,
					youtubeId: null,
					youtubeTitle: null,
					youtubeThumb: null
				});
			} else {
				alert(data.error);
			}
			mediaConsole.endUpload();
		},'json')
	},

	render: function(){
		if(this.props.mode==='image')
			return	<div className="media-input">
						<div className="image-input dropZone" ref="mediaDropZone" key="image-input">
							Drop file here to add image
							<input type="button" onClick={this.handleCloseBtn} className="btn btn-white btn-info pull-right close-input-btn" value="x"/>
							{ this.state.uploadingFile ? <span>(uploaded {parseInt(this.state.uploadProgress)}%)</span> : null }
						</div>
						<div className="hide" ref="dropResult"></div>
					</div>;

		if(this.props.mode==='video')
			return	<div className="media-input video-input" key="youtube-input">
						<input type="text" onChange={this.getYoutubeData} ref="youtubeLink" style={{width:'400px'}} placeholder="Youtube link"/>
						{' '}
						{ this.state.fetchingYoutube ? 
							<span className="label label-warning">
								<img src="/imgs/ajax-loading-circle-orange.gif"/>{' Fetching data...'}
							</span>
							: null
						}
						{this.state.isValidYoutube ? <input type="button" className="btn btn-white btn-danger" onClick={this.saveYoutube} value="Add"/> : null}
						<input type="button" onClick={this.handleCloseBtn} className="btn btn-white btn-info pull-right close-input-btn" value="x"/>
						{
							this.state.isValidYoutube ?
								<div className="dropZone" ref="youtubeResult">
									<h4 style={{margin:0}}>{this.state.youtubeTitle}</h4>
									<img src={this.state.youtubeThumb} />
									{' or '}
									<input type="file" ref="youtubeSS"/>
								</div>
							: null
						}
					</div>;

		if(this.props.mode==='360')
			return	<div className="media-input">
						<div className="input-360 dropZone" ref="mediaDropZone" key="360-input">
							Drop file here to add 360 image
							<input type="button" onClick={this.handleCloseBtn} className="btn btn-white btn-info pull-right close-input-btn" value="x"/>
							{ this.state.uploadingFile ? <span>(uploaded {parseInt(this.state.uploadProgress)}%)</span> : null }
						</div>
						<div className="hide" ref="dropResult"></div>
					</div>;
		return <div/>
	}
});

var mediaConsoles = [];
$(function(){
	$(document).on('click', '.trash-content', function(e){
		e.preventDefault();
		var p = $(this).parents('.content');
		confirm('Move this content to trash?')
		&& $.post('/products/set-content/move-to-trash/'+p.data('id'),function(result){
			result.success && p.remove();
		},'json');
	});
	$(window).on('beforeunload',function(){
		for(var i in mediaConsoles)
			if(mediaConsoles[i].isDirty())
				return 'You have unsaved modification. Are you sure to leave without saving?';
		return null;
	})

	$('.media-console').each(function(){
		var o = $(this);
		mediaConsoles[o.data('type')+'-'+o.data('id')] = React.renderComponent(<MediaConsole console={this} model={o.data('type')} id={o.data('id')} mediaList={o.parents('.media-row').find('.media-list')} />, o.get(0));
	});

	$('.media-list').each(function(){
		var o = $(this),
			list = o.children(),
			items = [],
			template = _.template($('#media-content-template').html());
		list.each(function(){
			var c = $(this);
			items.push(template({
				media_id: c.data('id'),
				mode: c.data('mode'),
				thumb: c.data('thumb'),
				link: c.data('link'),
				src: c.data('path')
			}));
		});
		o.html(items.join(''));
	});
})