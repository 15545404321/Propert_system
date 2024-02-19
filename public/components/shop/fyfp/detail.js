Vue.component('Detail', {
	template: `
		<el-dialog title="查看详情" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<table cellpadding="0" cellspacing="0" class="table table-bordered" align="center" width="100%" style="word-break:break-all; margin-bottom:30px;  font-size:13px;">
				<tbody>
					<tr>
						<td class="title" width="100">房间编号：</td>
						<td>
							{{form.fyfp_fjbh}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">建筑面积：</td>
						<td>
							{{form.fyfp_jzmj}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">房屋类型：</td>
						<td>
							{{form.fyfp_fwlx}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">房屋状态：</td>
						<td>
							{{form.fyfp_fwzt}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用类型：</td>
						<td>
							{{form.fyfp_fylx}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">计算标准：</td>
						<td>
							{{form.fyfp_jsbz}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">计算公式：</td>
						<td>
							{{form.fyfp_jsgs}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">费用单价：</td>
						<td>
							{{form.fyfp_fydj}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">房间系数：</td>
						<td>
							{{form.fyfp_fzxs}}
						</td>
					</tr>
					<tr>
						<td class="title" width="100">滞纳金：</td>
						<td>
							{{form.fyfp_znj}}
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
			axios.post(base_url+'/Fyfp/detail',this.info).then(res => {
				this.form = res.data.data
			})
		},
		closeForm(){
			this.$emit('update:show', false)
		}
	}
})
