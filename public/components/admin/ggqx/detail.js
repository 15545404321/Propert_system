Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">权限名称：</td>
						<td>
							{{form.ggqx_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">权限地址：</td>
						<td>
							{{form.ggqx_url}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开发人员：</td>
						<td>
							{{form.ggqx_kfry}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">功能说明：</td>
						<td>
							{{form.ggqx_beizhu}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">创建时间：</td>
						<td>
							{{parseTime(form.ggqx_time)}}
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
			axios.post(base_url+'/Ggqx/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
