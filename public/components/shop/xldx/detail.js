Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">车位编号：</td>
						<td>
							{{form.cewei_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用定义：</td>
						<td>
							{{form.fydy_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用标准：</td>
						<td>
							{{form.fybz_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">生成方式：</td>
						<td>
							<span v-if="form.cwfy_scfs == '1'">按月生成</span>
							<span v-if="form.cwfy_scfs == '2'">按日生成</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">生成类型：</td>
						<td>
							<span v-if="form.cwfy_sclx == '1'">【按每月30天计算】</span>
							<span v-if="form.cwfy_sclx == '2'">【按每月实际天数计算】</span>
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开始时间：</td>
						<td>
							{{parseTime(form.cwfy_kstime,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">终止时间：</td>
						<td>
							{{parseTime(form.cwfy_zztime,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">开始月份：</td>
						<td>
							{{parseTime(form.cwfy_ksmonth)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">终止月份：</td>
						<td>
							{{parseTime(form.cwfy_zzmonth)}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">客户id：</td>
						<td>
							{{form.member_id}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">多选：</td>
						<td>
								{{formatStr(form.dxxx,'[{"key":"男","val":"1","label_color":"primary"}]')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">多选2：</td>
						<td>
							{{form.duoxuan}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">文件：</td>
						<td>
						<el-link v-if="form.xldx_wenjian" style="font-size:13px;" :href="form.xldx_wenjian" target="_blank">下载附件</el-link>
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
			axios.post(base_url+'/Xldx/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
