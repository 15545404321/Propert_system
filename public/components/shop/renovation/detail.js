Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">导航标题：</td>
						<td>
							{{form.renovation_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">导航图片：</td>
						<td>
							<el-image v-if="form.renovation_image" class="table_list_pic" :src="form.renovation_image"  :preview-src-list="[form.renovation_image]"></el-image>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">导航页面：</td>
						<td>
							{{form.renovation_page}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">导航简介：</td>
						<td>
							{{form.renovation_synopsis}}
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
			axios.post(base_url+'/Renovation/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
