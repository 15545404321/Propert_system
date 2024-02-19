Vue.component('Update', {
	template: `
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="资产全称" prop="fcxx_id">
							<el-input  v-model="form.fcxx_id" autoComplete="off" clearable  placeholder="请输入资产全称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="客户名称" prop="member_id">
							<el-input  v-model="form.member_id" autoComplete="off" clearable  placeholder="请输入客户名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用名称" prop="fydy_id">
							<el-input  v-model="form.fydy_id" autoComplete="off" clearable  placeholder="请输入费用名称"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="计费标准" prop="fybz_id">
							<el-input  v-model="form.fybz_id" autoComplete="off" clearable  placeholder="请输入计费标准"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="单次应收" prop="zjys_dcys">
							<el-input  v-model="form.zjys_dcys" autoComplete="off" clearable  placeholder="请输入单次应收"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="使用数量" prop="zjys_sysl">
							<el-input  v-model="form.zjys_sysl" autoComplete="off" clearable  placeholder="请输入使用数量"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="本次应收" prop="zjys_bcys">
							<el-input  v-model="form.zjys_bcys" autoComplete="off" clearable  placeholder="请输入本次应收"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="开始日期" prop="zjys_ktime">
							<el-input  v-model="form.zjys_ktime" autoComplete="off" clearable  placeholder="请输入开始日期"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="结束时间" prop="zjys_jtime">
							<el-input  v-model="form.zjys_jtime" autoComplete="off" clearable  placeholder="请输入结束时间"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="追加摘要" prop="zjys_zjzy">
							<el-input  v-model="form.zjys_zjzy" autoComplete="off" clearable  placeholder="请输入追加摘要"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属物业" prop="shop_id">
							<el-input  v-model="form.shop_id" autoComplete="off" clearable  placeholder="请输入所属物业"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="所属小区" prop="xqgl_id">
							<el-input  v-model="form.xqgl_id" autoComplete="off" clearable  placeholder="请输入所属小区"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用类型" prop="fylx_id">
							<el-input  v-model="form.fylx_id" autoComplete="off" clearable  placeholder="请输入费用类型"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
			</el-form>
			<div slot="footer" class="dialog-footer">
				<el-button :size="size" :loading="loading" type="primary" @click="submit" >
					<span v-if="!loading">确 定</span>
					<span v-else>提 交 中...</span>
				</el-button>
				<el-button :size="size" @click="closeForm">取 消</el-button>
			</div>
		</el-dialog>
	`
	,
	components:{
	},
	props: {
		show: {
			type: Boolean,
			default: false
		},
		size: {
			type: String,
			default: 'small'
		},
		info: {
			type: Object,
		},
	},
	data(){
		return {
			form: {
				fcxx_id:'',
				member_id:'',
				fydy_id:'',
				fybz_id:'',
				zjys_dcys:'',
				zjys_sysl:'',
				zjys_bcys:'',
				zjys_ktime:'',
				zjys_jtime:'',
				zjys_zjzy:'',
				shop_id:'',
				xqgl_id:'',
				fylx_id:'',
			},
			loading:false,
			rules: {
			}
		}
	},
	methods: {
		open(){
			this.form = this.info
		},
		submit(){
			this.$refs['form'].validate(valid => {
				if(valid) {
					this.loading = true
					axios.post(base_url + '/Ysk/update',this.form).then(res => {
						if(res.data.status == 200){
							this.$message({message: res.data.msg, type: 'success'})
							this.$emit('refesh_list')
							this.closeForm()
						}else{
							this.loading = false
							this.$message.error(res.data.msg)
						}
					}).catch(()=>{
						this.loading = false
					})
				}
			})
		},
		closeForm(){
			this.$emit('update:show', false)
			this.loading = false
			if (this.$refs['form']!==undefined) {
				this.$refs['form'].resetFields()
			}
		},
	}
})
