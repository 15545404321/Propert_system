Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">资产全称：</td>
						<td>
							{{form.fcxx_fjbh}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">客户名称：</td>
						<td>
							{{form.member_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用名称：</td>
						<td>
							{{form.fydy_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">计费标准：</td>
						<td>
							{{form.fybz_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">单次应收：</td>
						<td>
							{{form.zjys_dcys}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">使用数量：</td>
						<td>
							{{form.zjys_sysl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">本次应收：</td>
						<td>
							{{form.zjys_bcys}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开始日期：</td>
						<td>
							{{form.zjys_ktime}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结束时间：</td>
						<td>
							{{form.zjys_jtime}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">追加摘要：</td>
						<td>
							{{form.zjys_zjzy}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">所属物业：</td>
						<td>
							{{form.shop_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">所属小区：</td>
						<td>
							{{form.xqgl_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用类型：</td>
						<td>
							{{form.fylx_id}}
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
			axios.post(base_url+'/Ysk/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
