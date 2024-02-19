Vue.component('Update', {
	template: `
		<el-dialog title="修改" width="600px" class="icon-dialog" :visible.sync="show" @open="open" :before-close="closeForm" append-to-body>
			<el-form :size="size" ref="form" :model="form" :rules="rules" :label-width=" ismobile()?'90px':'16%'">
				<el-row >
					<el-col :span="24">
						<el-form-item label="房间编号" prop="fyfp_fjbh">
							<el-select   style="width:100%" v-model="form.fyfp_fjbh" filterable clearable placeholder="请选择房间编号">
								<el-option v-for="(item,i) in fyfp_fjbhs" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="建筑面积" prop="fyfp_jzmj">
							<el-input  v-model="form.fyfp_jzmj" autoComplete="off" clearable  placeholder="请输入建筑面积"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="房屋类型" prop="fyfp_fwlx">
							<el-select   style="width:100%" v-model="form.fyfp_fwlx" filterable clearable placeholder="请选择房屋类型">
								<el-option v-for="(item,i) in fyfp_fwlxs" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="房屋状态" prop="fyfp_fwzt">
							<el-input  v-model="form.fyfp_fwzt" autoComplete="off" clearable  placeholder="请输入房屋状态"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用类型" prop="fyfp_fylx">
							<el-select   style="width:100%" v-model="form.fyfp_fylx" filterable clearable placeholder="请选择费用类型">
								<el-option v-for="(item,i) in fyfp_fylxs" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="计算标准" prop="fyfp_jsbz">
							<el-select   style="width:100%" v-model="form.fyfp_jsbz" filterable clearable placeholder="请选择计算标准">
								<el-option v-for="(item,i) in fyfp_jsbzs" :key="i" :label="item.key" :value="item.val"></el-option>
							</el-select>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="计算公式" prop="fyfp_jsgs">
							<el-input  v-model="form.fyfp_jsgs" autoComplete="off" clearable  placeholder="请输入计算公式"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="费用单价" prop="fyfp_fydj">
							<el-input  v-model="form.fyfp_fydj" autoComplete="off" clearable  placeholder="请输入费用单价"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="房间系数" prop="fyfp_fzxs">
							<el-input  v-model="form.fyfp_fzxs" autoComplete="off" clearable  placeholder="请输入房间系数"></el-input>
						</el-form-item>
					</el-col>
				</el-row>
				<el-row >
					<el-col :span="24">
						<el-form-item label="滞纳金" prop="fyfp_znj">
							<el-input  v-model="form.fyfp_znj" autoComplete="off" clearable  placeholder="请输入滞纳金"></el-input>
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
				fyfp_fjbh:'',
				fyfp_jzmj:'',
				fyfp_fwlx:'',
				fyfp_fwzt:'',
				fyfp_fylx:'',
				fyfp_jsbz:'',
				fyfp_jsgs:'',
				fyfp_fydj:'',
				fyfp_fzxs:'',
				fyfp_znj:'',
			},
			fyfp_fjbhs:[],
			fyfp_fwlxs:[],
			fyfp_fylxs:[],
			fyfp_jsbzs:[],
			loading:false,
			rules: {
			}
		}
	},
	watch:{
		show(val){
			if(val){
				axios.post(base_url + '/Fyfp/getFieldList').then(res => {
					if(res.data.status == 200){
						this.fyfp_fjbhs = res.data.data.fyfp_fjbhs
						this.fyfp_fwlxs = res.data.data.fyfp_fwlxs
						this.fyfp_fylxs = res.data.data.fyfp_fylxs
						this.fyfp_jsbzs = res.data.data.fyfp_jsbzs
					}
				})
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
					axios.post(base_url + '/Fyfp/update',this.form).then(res => {
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
