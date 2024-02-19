Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">所属项目：</td>
						<td>
							{{form.xqgl.xqgl_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">员工姓名：</td>
						<td>
							{{form.shopadmin.cname}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结算月份：</td>
						<td>
							{{parseTime(form.xz_ffdate)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">结算周期：</td>
						<td>
							{{form.gz_zhouqi}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">发放金额：</td>
						<td>
							{{form.gz_jine}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">工资明细：</td>
						<td>
							{{form.gz_mingxi}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">发放批次：</td>
						<td>
							{{form.xzpici_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">生成时间：</td>
						<td>
							{{parseTime(form.addtime)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">考勤审核：</td>
						<td>
							<span v-if="form.gz_kqsh == '1'">已审</span>
							<span v-if="form.gz_kqsh == '0'">待审</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">会计审核：</td>
						<td>
							<span v-if="form.gz_kjsh == '1'">已审</span>
							<span v-if="form.gz_kjsh == '0'">待审</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">总经理审核：</td>
						<td>
							<span v-if="form.gz_zjlsh == '1'">已审</span>
							<span v-if="form.gz_zjlsh == '0'">待审</span>
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
				shopadmin:{},
			},
		}
	},
	methods: {
		open(){
			axios.post(base_url+'/Gongzi/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
