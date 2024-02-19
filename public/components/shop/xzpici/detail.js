Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">结算月份：</td>
						<td>
							{{parseTime(form.xz_ffdate)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结算周期：</td>
						<td>
							{{form.xz_zhouqi}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">生成时间：</td>
						<td>
							{{parseTime(form.addtime)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结算人数：</td>
						<td>
							{{form.xz_ren}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结算金额：</td>
						<td>
							{{form.xz_jine}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结算项目：</td>
						<td>
							{{form.xqgl_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">操作会计：</td>
						<td>
							{{form.cname}}
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
			axios.post(base_url+'/Xzpici/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
