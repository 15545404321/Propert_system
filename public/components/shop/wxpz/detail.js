Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">APPID：</td>
						<td>
							{{form.app_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">Secret：</td>
						<td>
							{{form.secret}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">支付商户：</td>
						<td>
							{{form.mch_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">支付秘钥：</td>
						<td>
							{{form.pay_sign_key}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">支付证书：</td>
						<td>
						<el-link v-if="form.apiclient_cert" style="font-size:13px;" :href="form.apiclient_cert" target="_blank">下载附件</el-link>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">证书密钥：</td>
						<td>
						<el-link v-if="form.apiclient_key" style="font-size:13px;" :href="form.apiclient_key" target="_blank">下载附件</el-link>
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
			axios.post(base_url+'/Wxpz/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
