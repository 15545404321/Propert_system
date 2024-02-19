Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">计费名称：</td>
						<td>
							{{form.fybz_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">计费公式：</td>
						<td>
							{{form.jfgs_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">标准单价：</td>
						<td>
							{{form.fybz_bzdj}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">计费系数：</td>
						<td>
							{{form.fybz_jfxs}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">坏账率：</td>
						<td>
							{{form.fybz_hzl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">状态：</td>
						<td>
							<span v-if="form.fybz_status == '1'">正常</span>
							<span v-if="form.fybz_status == '0'">禁用</span>
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
			axios.post(base_url+'/Fybz/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
