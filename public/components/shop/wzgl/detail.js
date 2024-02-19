Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="800px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">文章标题：</td>
						<td>
							{{form.wzgl_title}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">文章首图：</td>
						<td>
							<el-image v-if="form.wzgl_img" class="table_list_pic" :src="form.wzgl_img"  :preview-src-list="[form.wzgl_img]"></el-image>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">文章简述：</td>
						<td>
							{{form.wzgl_futitle}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">发布时间：</td>
						<td>
							{{parseTime(form.wzgl_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">所属分类：</td>
						<td>
							{{form.wzfl_id}}
						</td>
					</tr>
				</tbody>
			</table>
		</el-dialog>
	`
	,
	props: {
		show: {
			type: Boolean,
			default: true
		},
		size: {
			type: String,
			default: 'mini'
		},
		info: {
			type: Object,
		},
	},
	data() {
		return {
			form:{
			},
		}
	},
	methods: {
		open(){
			axios.post(base_url+'/Wzgl/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
