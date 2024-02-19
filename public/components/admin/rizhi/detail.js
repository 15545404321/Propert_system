Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">所属功能：</td>
						<td>
							{{form.rz_gongneng}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">方法名称：</td>
						<td>
							{{form.rz_fangfa}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">记录状态：</td>
						<td>
							<span v-if="form.rz_status == '1'">打开</span>
							<span v-if="form.rz_status == '0'">关闭</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">添加日期：</td>
						<td>
							{{parseTime(form.rz_time)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">操作人：</td>
						<td>
							{{form.user_id}}
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
			axios.post(base_url+'/Rizhi/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
