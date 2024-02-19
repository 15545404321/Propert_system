Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">费用名称：</td>
						<td>
							{{form.yssj_fymc}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">财务月份：</td>
						<td>
							{{form.yssj_cwyf}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开始日期：</td>
						<td>
							{{parseTime(form.yssj_kstime,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">截至日期：</td>
						<td>
							{{parseTime(form.yssj_jztiem,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用单价：</td>
						<td>
							{{form.yssj_fydj}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">应收金额：</td>
						<td>
							{{form.yssj_ysje}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">违约金额：</td>
						<td>
							{{form.yssj_wyje}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">优惠金额：</td>
						<td>
							{{form.yssj_yhje}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">实收金额：</td>
						<td>
							{{form.yssj_shje}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用类型：</td>
						<td>
							{{form.fylx_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用标准：</td>
						<td>
							{{form.fybz_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">付款状态：</td>
						<td>
							<span v-if="form.yssj_stuats == '1'">开启</span>
							<span v-if="form.yssj_stuats == '0'">关闭</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">付款时间：</td>
						<td>
							{{parseTime(form.yssj_fksj)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">应收ID：</td>
						<td>
							{{form.ys_id}}
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
			axios.post(base_url+'/Yssj/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
