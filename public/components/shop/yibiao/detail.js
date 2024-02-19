Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">仪表编号：</td>
						<td>
							{{form.yibiao_sn}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">仪表类型：</td>
						<td>
							{{form.yblx_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">仪表种类：</td>
						<td>
							{{form.ybzl_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">楼宇/单元：</td>
						<td>
							{{form.louyu_name}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">房间编号：</td>
						<td>
							{{form.fcxx_fjbh}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">仪表倍率：</td>
						<td>
							{{form.yibiao_ybbl}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">初始底数：</td>
						<td>
							{{form.yibiao_csds}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">仪表量程：</td>
						<td>
							{{form.yibiao_yblc}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">安装时间：</td>
						<td>
							{{parseTime(form.add_time,'{y}-{m}-{d}')}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">仪表状态：</td>
						<td>
							<span v-if="form.yibiao_status == '1'">正常</span>
							<span v-if="form.yibiao_status == '0'">停用</span>
							<span v-if="form.yibiao_status == '2'">换表停用</span>
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
			axios.post(base_url+'/YiBiao/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
