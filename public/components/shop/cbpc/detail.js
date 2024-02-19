Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">财务月份：</td>
						<td>
							{{parseTime(form.cbpc_cwyf)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开始日期：</td>
						<td>
							{{parseTime(form.cbpc_kstime,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结束日期：</td>
						<td>
							{{parseTime(form.cbpc_jstime,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">楼座编号：</td>
						<td>
							{{form.louyu_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">仪表类型：</td>
						<td>
							{{form.yblx_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">仪表种类：</td>
						<td>
							{{form.ybzl_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">入账状态：</td>
						<td>
							<span v-if="form.cbpc_status == '1'">已入账</span>
							<span v-if="form.cbpc_status == '0'">未入账</span>
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
			axios.post(base_url+'/Cbpc/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
