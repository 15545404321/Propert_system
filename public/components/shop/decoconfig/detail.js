Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">页面名称：</td>
						<td>
							{{form.decoconfig_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">页面路径：</td>
						<td>
							{{form.decoconfig_url}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">页面属性：</td>
						<td>
							<span v-if="form.decoconfig_type == '1'">tab页</span>
							<span v-if="form.decoconfig_type == '2'">普通页</span>
							<span v-if="form.decoconfig_type == '3'">弹窗</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">页面备注：</td>
						<td>
							{{form.decoconfig_remark}}
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
			axios.post(base_url+'/DecoConfig/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
