Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">钥匙名称：</td>
						<td>
							{{form.ysfl_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">领用时间：</td>
						<td>
							{{parseTime(form.ys_lingyong)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">领用人员：</td>
						<td>
							{{form.ys_user}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">使用状态：</td>
						<td>
							<span v-if="form.ys_state == '1'">使用</span>
							<span v-if="form.ys_state == '2'">归还</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">归还时间：</td>
						<td>
							{{parseTime(form.ys_ghtime)}}
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
			axios.post(base_url+'/Ysly/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
