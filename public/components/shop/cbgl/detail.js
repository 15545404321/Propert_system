Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">住户姓名：</td>
						<td>
							{{form.member_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">房间编号：</td>
						<td>
							{{form.fcxx_fjbh}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">仪表编号：</td>
						<td>
							{{form.yibiao_sn}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">财务月份：</td>
						<td>
							{{parseTime(form.cbgl_cwyf)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">上期数量：</td>
						<td>
							{{form.cbgl_sqsl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">本期数量：</td>
						<td>
							{{form.cbgl_bqsl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">抄表用量：</td>
						<td>
							{{form.cbgl_cbyl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">损耗用量：</td>
						<td>
							{{form.cbgl_shyl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">仪表倍率：</td>
						<td>
							{{form.cbgl_ybbl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">实际用量：</td>
						<td>
							{{form.cbgl_sjyl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">标准单价：</td>
						<td>
							{{form.fybz_bzdj}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">抄表金额：</td>
						<td>
							{{form.cbgl_cbje}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开始时间：</td>
						<td>
							{{parseTime(form.cbgl_kstime,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结束时间：</td>
						<td>
							{{parseTime(form.cbgl_jstime,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">入账状态：</td>
						<td>
							<span v-if="form.cbgl_status == '1'">已入账</span>
							<span v-if="form.cbgl_status == '0'">未入账</span>
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
			axios.post(base_url+'/Cbgl/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
